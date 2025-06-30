<?php

namespace Modules\ProductCatalogue\Http\Controllers;

use Carbon;
use App\Media;
use App\Banner;
use App\Contact;
use App\Product;
use App\Business;
use App\Category;
use App\Discount;
use App\Variation;
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use App\SellingPriceGroup;
use App\Utils\ContactUtil;
use App\Utils\ProductUtil;
use App\Utils\BusinessUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Utils\TransactionUtil;
use App\Utils\CashRegisterUtil;
use App\Utils\NotificationUtil;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Events\SellCreatedOrModified;

class ProductCatalogueController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;

    protected $moduleUtil;

    protected $contactUtil;


    protected $businessUtil;

    protected $transactionUtil;

    protected $cashRegisterUtil;


    protected $notificationUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(
        ContactUtil $contactUtil,
        ProductUtil $productUtil,
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        CashRegisterUtil $cashRegisterUtil,
        ModuleUtil $moduleUtil,
        NotificationUtil $notificationUtil
    ) {
        $this->contactUtil = $contactUtil;
        $this->productUtil = $productUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->moduleUtil = $moduleUtil;
        $this->notificationUtil = $notificationUtil;

        $this->dummyPaymentLine = ['method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => ''];
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($business_id, $location_id)
    {
        $products = Product::where('business_id', $business_id)
                ->whereHas('product_locations', function ($q) use ($location_id) {
                    $q->where('product_locations.location_id', $location_id);
                })
                ->ProductForSales()
                ->with(['variations', 'variations.product_variation', 'category'])
                ->get()
                ->groupBy('category_id');
        $business = Business::with(['currency'])->findOrFail($business_id);
        $business_location = BusinessLocation::where('business_id', $business_id)->findOrFail($location_id);

        $now = \Carbon::now()->toDateTimeString();
        $discounts = Discount::where('business_id', $business_id)
                                ->where('location_id', $location_id)
                                ->where('is_active', 1)
                                ->where('starts_at', '<=', $now)
                                ->where('ends_at', '>=', $now)
                                ->orderBy('priority', 'desc')
                                ->get();
        foreach ($discounts as $key => $value) {
            $discounts[$key]->discount_amount = $this->productUtil->num_f($value->discount_amount, false, $business);
        }

        $categories = Category::forDropdown($business_id, 'product');

        $banners = Banner::where('business_id', $business_id)
        ->where('is_active', 1)
        ->orderBy('order', 'asc')
        ->get();

        $cartItems = session()->get('cart', []);
        $totalItems = count($cartItems);


        // session()->forget('cart');



        return view('productcatalogue::catalogue.index')->with(compact('products','totalItems','banners','business', 'discounts', 'business_location', 'categories'));
    }


    public function addToCart(Request $request)
    {
        $request->validate([
            'variation_id' => 'required|exists:variations,id',
            'location_id' => 'required|exists:business_locations,id',
            'business_id' => 'required|exists:business,id',
            'name' => 'nullable',
            'image' => 'nullable',
        ]);

        $variation = Variation::find($request->variation_id);

        if (!$variation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid variation selected.'
            ], 400);
        }

        // Create a new cart item
        $cartItem = [
            'location_id' => $request->location_id,
            'name' => $request->name,
            'image' => $request->image,
            'variation_id' => $request->variation_id,
            'business_id' => $request->business_id,
            'quantity' => 1,
            'price' => $variation->sell_price_inc_tax
        ];

        // Retrieve the cart from the session
        $cart = session()->get('cart', []);
        $totalItems = count($cart);

        // Check if item already exists in cart based on variation_id, location_id, and business_id
        $exists = false;
        foreach ($cart as $key => $item) {
            if ($item['variation_id'] == $cartItem['variation_id'] && $item['location_id'] == $cartItem['location_id'] && $item['business_id'] == $cartItem['business_id']) {
                // Item exists, increment the quantity
                $cart[$key]['quantity'] += 1;
                $exists = true;
                break;
            }
        }

        // If the item doesn't exist in the cart, add it
        if (!$exists) {
            $cart[] = $cartItem;
        }

        // Save the updated cart back to the session
        session()->put('cart', $cart);

        $cartItems = session()->get('cart', []);
        $totalItems = count($cartItems);

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart successfully!',
            'cart' => $cart,
            'totalItems'=> $totalItems,
        ]);
    }



    public function updateCart(Request $request)
    {
        $variationId = $request->input('variation_id');
        $quantity = $request->input('quantity');

        $cart = session()->get('cart', []);

        foreach ($cart as $key => $item) {
            if ((int)$item['variation_id'] === (int)$variationId) {
                $cart[$key]['quantity'] = $quantity;
                break;
            }
        }

        session()->put('cart', $cart);

        return response()->json(['message' => 'Cart updated successfully!']);
    }





    public function deleteCartItem(Request $request)
    {
        $variationId = $request->input('variation_id');

        $cart = session()->get('cart', []);

        foreach ($cart as $key => $item) {
            if ((int)$item['variation_id'] === (int)$variationId) {
                unset($cart[$key]);
                break;
            }
        }

        session()->put('cart', $cart);

        return response()->json(['message' => 'Item removed from the cart successfully!']);
    }


    public function viewCart($business_id, $location_id)
    {
        $business = Business::with(['currency'])->findOrFail($business_id);
        $business_location = BusinessLocation::where('business_id', $business_id)->findOrFail($location_id);
        $items = session()->get('cart', []);
        $itemss = session()->get('cart', []);

        $total = 0;
        $cartItemsWithDetails = [];

        foreach ($items as $item) {
            $variation = Variation::with('product')->find($item['variation_id']);
            $sub_units = $this->productUtil->getSubUnits($business_id, $variation->product->unit_id, false, $variation->product->id);

            $multiplier = 1;
            foreach ($sub_units as $key => $value) {
                if (!empty($variation->product->sub_unit_id) && $variation->product->sub_unit_id == $key) {
                    $multiplier = $value['multiplier'];
                }
            }

            $itemTotal = $item['price'] * $item['quantity'];
            $total += $itemTotal;

            $cartItemsWithDetails[] = [
                'product_type' => $variation ? $variation->product->type : null,
                'sell_line_note' => $variation ? $variation->product->sell_line_note : null,
                'product_id' => $variation ? $variation->product->id : null,
                'variation_id' => $variation ? $variation->id : null,
                'enable_stock' => $variation ? $variation->product->enable_stock : null,
                'quantity' => $item['quantity'],
                'product_unit_id' => $variation ? $variation->product->unit_id : null,
                // 'sub_unit_id' => $variation ? $variation->product->sub_unit_id : null,
                'base_unit_multiplier' => $multiplier,
                'unit_price' => $variation->default_sell_price,
                'line_discount_amount' => $item['line_discount_amount'] ?? '0.00',
                'line_discount_type' => $item['line_discount_type'] ?? 'fixed',
                'item_tax' => $item['item_tax'] ?? '0.00',
                'tax_id' => $item['tax_id'] ?? null,
                'unit_price_inc_tax' => $variation->sell_price_inc_tax,
            ];
        }

        return view('productcatalogue::catalogue.view')
            ->with(compact('business', 'total','itemss', 'cartItemsWithDetails', 'business_location'));
    }


    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($business_id, $id)
    {
        $product = Product::with(['brand', 'unit', 'category', 'sub_category', 'product_tax', 'variations', 'variations.product_variation', 'variations.group_prices', 'variations.media', 'product_locations', 'warranty'])->where('business_id', $business_id)
                        ->findOrFail($id);

        $price_groups = SellingPriceGroup::where('business_id', $product->business_id)->active()->pluck('name', 'id');

        $allowed_group_prices = [];
        foreach ($price_groups as $key => $value) {
            $allowed_group_prices[$key] = $value;
        }

        $group_price_details = [];
        $discounts = [];
        foreach ($product->variations as $variation) {
            foreach ($variation->group_prices as $group_price) {
                $group_price_details[$variation->id][$group_price->price_group_id] = $group_price->price_inc_tax;
            }

            $discounts[$variation->id] = $this->productUtil->getProductDiscount($product, $product->business_id, request()->input('location_id'), false, null, $variation->id);
        }

        $combo_variations = [];
        if ($product->type == 'combo') {
            $combo_variations = $this->productUtil->__getComboProductDetails($product['variations'][0]->combo_variations, $product->business_id);
        }

        $location_id=request()->input('location_id');

        return view('productcatalogue::catalogue.show')->with(compact(
            'product',
            'allowed_group_prices',
            'group_price_details',
            'combo_variations',
            'location_id',
            'discounts'
        ));
    }


    public function contactSave(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:20',
            'business_id' => 'required',
        ]);

        $existingContact = Contact::where('email', $validatedData['email'])->first();

        $user = DB::table('users')
        ->where('business_id', $request->business_id)
        ->orderBy('id')
        ->first();

        if ($user) {
            $user_id = $user->id;
        } else {
            $user_id = null;
        }

        if ($existingContact) {
            return response()->json(['contact_id' => $existingContact->id]);
        } else {
            $contact = new Contact();
            $contact->name = $validatedData['name'];
            $contact->address_line_1 = $validatedData['address_line_1'];
            $contact->email = $validatedData['email'];
            $contact->mobile = $validatedData['phone'];
            $ref_count = $this->productUtil->setAndGetReferenceCount('contacts', $validatedData['business_id']);
            $contact->contact_id =$this->productUtil->generateReferenceNumber('contacts', $ref_count, $validatedData['business_id']);
            $contact->created_by=$user_id;
            $contact->type='customer';
            $contact->business_id = $validatedData['business_id'];
            $contact->save();

            return response()->json(['contact_id' => $contact->id]);
        }

    }
    public function generateQr()
    {
        $business_id = request()->session()->get('user.business_id');
        if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'productcatalogue_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $business = Business::findOrFail($business_id);

        return view('productcatalogue::catalogue.generate_qr')
                    ->with(compact('business_locations', 'business'));
    }


    public function updateAccount(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:business,id',
            'bank_account' => 'required|string|max:255',
        ]);

        $business = Business::findOrFail($request->input('business_id'));

        // Update the bank account field
        $business->bank_account = $request->input('bank_account');
        $business->save();

        return redirect()->back()->with('success', 'Bank account updated successfully.');
    }

    public function submit(Request $request)
    {
        $is_direct_sale = false;
        if (!empty($request->input('is_direct_sale'))) {
            $is_direct_sale = true;
        }

        //Check if there is a open register, if no then redirect to Create Register screen.
        if (!$is_direct_sale && $this->cashRegisterUtil->countOpenedRegister() == 0) {
            return redirect()->action([\App\Http\Controllers\CashRegisterController::class, 'create']);
        }

        try {
            $input = $request->except('_token');

            $input['is_quotation'] = 0;
            //status is send as quotation from Add sales screen.
            if ($input['status'] == 'quotation') {
                $input['status'] = 'draft';
                $input['is_quotation'] = 1;
                $input['sub_status'] = 'quotation';
            } elseif ($input['status'] == 'proforma') {
                $input['status'] = 'draft';
                $input['sub_status'] = 'proforma';
            }

            //Add change return
            $change_return = $this->dummyPaymentLine;
            if (!empty($input['payment']['change_return'])) {
                $change_return = $input['payment']['change_return'];
                unset($input['payment']['change_return']);
            }

            //Check Customer credit limit
            $is_credit_limit_exeeded = $this->transactionUtil->isCustomerCreditLimitExeeded($input);

            if ($is_credit_limit_exeeded !== false) {
                $credit_limit_amount = $this->transactionUtil->num_f($is_credit_limit_exeeded, true);
                $output = ['success' => 0,
                    'msg' => __('lang_v1.cutomer_credit_limit_exeeded', ['credit_limit' => $credit_limit_amount]),
                ];
                if (!$is_direct_sale) {
                    return $output;
                } else {
                    return back()
                        ->with('status', $output);
                }
            }


            if (!empty($input['products'])) {
                $business_id = $request->business_id;

                $user = DB::table('users')
                ->where('business_id', $business_id)
                ->orderBy('id')
                ->first();

                if ($user) {
                    $user_id = $user->id;
                } else {
                    $user_id = null;
                }


                $discount = ['discount_type' => $input['discount_type'],
                    'discount_amount' => $input['discount_amount'],
                ];
                $invoice_total = $this->productUtil->calculateInvoiceTotal($input['products'], $input['tax_rate_id'], $discount);

                DB::beginTransaction();

                $input['transaction_date'] = Carbon::now();
                //Set commission agent

                //Customer group details
                $contact_id = $request->get('contact_id', null);
                $cg = $this->contactUtil->getCustomerGroup($business_id, $contact_id);
                $input['customer_group_id'] = (empty($cg) || empty($cg->id)) ? null : $cg->id;

                //set selling price group id
                $price_group_id = $request->has('price_group') ? $request->input('price_group') : null;

                //If default price group for the location exists
                $price_group_id = $price_group_id == 0 && $request->has('default_price_group') ? $request->input('default_price_group') : $price_group_id;

                $input['is_suspend'] = isset($input['is_suspend']) && 1 == $input['is_suspend'] ? 1 : 0;
                if ($input['is_suspend']) {
                    $input['sale_note'] = !empty($input['additional_notes']) ? $input['additional_notes'] : null;
                }

                //Generate reference number

                if (!empty($request->input('invoice_scheme_id'))) {
                    $input['invoice_scheme_id'] = $request->input('invoice_scheme_id');
                }

                $input['sale'] = $request->input('sale');

                if ($request->input('additional_expense_value_1') != '') {
                    $input['additional_expense_key_1'] = $request->input('additional_expense_key_1');
                    $input['additional_expense_value_1'] = $request->input('additional_expense_value_1');
                }

                if ($request->input('additional_expense_value_2') != '') {
                    $input['additional_expense_key_2'] = $request->input('additional_expense_key_2');
                    $input['additional_expense_value_2'] = $request->input('additional_expense_value_2');
                }

                if ($request->input('additional_expense_value_3') != '') {
                    $input['additional_expense_key_3'] = $request->input('additional_expense_key_3');
                    $input['additional_expense_value_3'] = $request->input('additional_expense_value_3');
                }

                if ($request->input('additional_expense_value_4') != '') {
                    $input['additional_expense_key_4'] = $request->input('additional_expense_key_4');
                    $input['additional_expense_value_4'] = $request->input('additional_expense_value_4');
                }

                $input['selling_price_group_id'] = $price_group_id;

                if ($this->transactionUtil->isModuleEnabled('tables')) {
                    $input['res_table_id'] = request()->get('res_table_id');
                }
                if ($this->transactionUtil->isModuleEnabled('service_staff')) {
                    $input['res_waiter_id'] = request()->get('res_waiter_id');
                }

                if ($this->transactionUtil->isModuleEnabled('kitchen')) {
                    $input['is_kitchen_order'] = request()->get('is_kitchen_order');
                }

                //upload document
                $input['document'] = $this->transactionUtil->uploadFile($request, 'sell_document', 'documents');

                $transaction = $this->transactionUtil->createSellTransaction($business_id, $input, $invoice_total, $user_id);

                //Upload Shipping documents
                Media::uploadMedia($business_id, $transaction, $request, 'shipping_documents', false, 'shipping_document');

                $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id']);

                $change_return['amount'] = $input['change_return'] ?? 0;
                $change_return['is_return'] = 1;

                $input['payment'][] = $change_return;

                $is_credit_sale = isset($input['is_credit_sale']) && $input['is_credit_sale'] == 1 ? true : false;

                if (!$transaction->is_suspend && !empty($input['payment']) && !$is_credit_sale) {
                    $this->transactionUtil->createOrUpdatePaymentLines($transaction, $input['payment']);
                }

                //Check for final and do some processing.
                session()->forget('cart');


                if (!empty($transaction->sales_order_ids)) {
                    $this->transactionUtil->updateSalesOrderStatus($transaction->sales_order_ids);
                }

                $this->moduleUtil->getModuleData('after_sale_saved', ['transaction' => $transaction, 'input' => $input]);

                Media::uploadMedia($business_id, $transaction, $request, 'documents');

                $this->transactionUtil->activityLog($transaction, 'added');

                DB::commit();

                SellCreatedOrModified::dispatch($transaction);

                if ($request->input('is_save_and_print') == 1) {
                    $url = $this->transactionUtil->getInvoiceUrl($transaction->id, $business_id);

                    return redirect()->to($url . '?print_on_load=true');
                }

                $msg = trans('sale.pos_sale_added');
                $receipt = '';
                $invoice_layout_id = $request->input('invoice_layout_id');
                $print_invoice = false;
                if (!$is_direct_sale) {
                    if ($input['status'] == 'draft') {
                        $msg = trans('sale.draft_added');

                        if ($input['is_quotation'] == 1) {
                            $msg = trans('lang_v1.quotation_added');
                            $print_invoice = true;
                        }
                    } elseif ($input['status'] == 'final') {
                        $print_invoice = true;
                    }
                }

                if ($transaction->is_suspend == 1 && empty($pos_settings['print_on_suspend'])) {
                    $print_invoice = false;
                }

                if (!auth()->user()->can('print_invoice')) {
                    $print_invoice = false;
                }

                if ($print_invoice) {
                    $receipt = $this->receiptContents($business_id, $input['location_id'], $transaction->id, null, false, true, $invoice_layout_id);
                }

                $output = ['success' => 1, 'msg' => $msg, 'receipt' => $receipt];

                if (!empty($whatsapp_link)) {
                    $output['whatsapp_link'] = $whatsapp_link;
                }
            } else {
                $output = ['success' => 0,
                    'msg' => trans('messages.something_went_wrong'),
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            dd('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            $msg = trans('messages.something_went_wrong');

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            }
            if (get_class($e) == \App\Exceptions\AdvanceBalanceNotAvailable::class) {
                $msg = $e->getMessage();
            }

            $output = ['success' => 0,
                'msg' => $msg,
            ];
        }

        if (!$is_direct_sale) {
            return $output;
        } else {
            return back();
            // if ($input['status'] == 'draft') {
            //     if (isset($input['is_quotation']) && $input['is_quotation'] == 1) {
            //         return redirect()
            //             ->action([\App\Http\Controllers\SellController::class, 'getQuotations'])
            //             ->with('status', $output);
            //     } else {
            //         return redirect()
            //             ->action([\App\Http\Controllers\SellController::class, 'getDrafts'])
            //             ->with('status', $output);
            //     }
            // } elseif ($input['status'] == 'quotation') {
            //     return redirect()
            //         ->action([\App\Http\Controllers\SellController::class, 'getQuotations'])
            //         ->with('status', $output);
            // } elseif (isset($input['type']) && $input['type'] == 'sales_order') {
            //     return redirect()
            //         ->action([\App\Http\Controllers\SalesOrderController::class, 'index'])
            //         ->with('status', $output);
            // } else {
            //     if (!empty($input['sub_type']) && $input['sub_type'] == 'repair') {
            //         $redirect_url = $input['print_label'] == 1 ? action([\Modules\Repair\Http\Controllers\RepairController::class, 'printLabel'], [$transaction->id]) : action([\Modules\Repair\Http\Controllers\RepairController::class, 'index']);

            //         return redirect($redirect_url)
            //             ->with('status', $output);
            //     }

            //     return redirect()
            //         ->action([\App\Http\Controllers\SellController::class, 'index'])
            //         ->with('status', $output);
            // }
        }
    }

    private function receiptContents(
        $business_id,
        $location_id,
        $transaction_id,
        $printer_type = null,
        $is_package_slip = false,
        $from_pos_screen = true,
        $invoice_layout_id = null,
        $is_delivery_note = false
    ) {
        $output = ['is_enabled' => false,
            'print_type' => 'browser',
            'html_content' => null,
            'printer_config' => [],
            'data' => [],
        ];

        $business_details = $this->businessUtil->getDetails($business_id);
        $location_details = BusinessLocation::find($location_id);

        if ($from_pos_screen && $location_details->print_receipt_on_invoice != 1) {
            return $output;
        }
        //Check if printing of invoice is enabled or not.
        //If enabled, get print type.
        $output['is_enabled'] = true;

        $invoice_layout_id = !empty($invoice_layout_id) ? $invoice_layout_id : $location_details->invoice_layout_id;
        $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $invoice_layout_id);

        //Check if printer setting is provided.
        $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;

        $receipt_details = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type);

        $currency_details = [
            'symbol' => $business_details->currency_symbol,
            'thousand_separator' => $business_details->thousand_separator,
            'decimal_separator' => $business_details->decimal_separator,
        ];
        $receipt_details->currency = $currency_details;

        if ($is_package_slip) {
            $output['html_content'] = view('sale_pos.receipts.packing_slip', compact('receipt_details'))->render();

            return $output;
        }

        if ($is_delivery_note) {
            $output['html_content'] = view('sale_pos.receipts.delivery_note', compact('receipt_details'))->render();

            return $output;
        }

        $output['print_title'] = $receipt_details->invoice_no;
        //If print type browser - return the content, printer - return printer config data, and invoice format config
        if ($receipt_printer_type == 'printer') {
            $output['print_type'] = 'printer';
            $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
            $output['data'] = $receipt_details;
        } else {
            $layout = !empty($receipt_details->design) ? 'sale_pos.receipts.' . $receipt_details->design : 'sale_pos.receipts.classic';

            $output['html_content'] = view($layout, compact('receipt_details'))->render();
        }

        return $output;
    }
}
