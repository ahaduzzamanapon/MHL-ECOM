<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card p-4">
            <div class="flex flex-wrap gap-y-5 items-end justify-between">
                <div>
                    <div class="flex flex-wrap items-start gap-y-2 gap-x-6 mb-5">
                        <p class="text-2xl font-medium">{{ $t('label.order_id') }}:
                            <span class="text-heading">
                                #{{ order.order_serial_no }}
                            </span>
                        </p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span
                                :class="'text-sm capitalize h-5 leading-5 px-2 rounded-3xl text-[#FB4E4E] bg-[#FFDADA]' + statusClass(order.payment_status)">
                                {{ enums.paymentStatusEnumArray[order.payment_status] }}
                            </span>
                            <span :class="'text-sm capitalize px-2 rounded-3xl ' + orderStatusClass(order.status)">
                                {{ enums.orderStatusEnumArray[order.status] }}
                            </span>
                        </div>
                    </div>
                    <ul class="flex flex-col gap-2">
                        <li class="flex items-center gap-2">
                            <i class="lab lab-line-calendar lab-font-size-16"></i>
                            <span class="text-xs">{{ order.order_datetime }}</span>
                        </li>
                        <li class="text-xs">
                            {{ $t('label.payment_type') }}:
                            <span class="text-heading">
                                {{ order.payment_method_name }}
                            </span>
                        </li>
                        <li class="text-xs">
                            {{ $t('label.order_type') }}:
                            <span class="text-heading">
                                {{ enums.orderTypeEnumArray[order.order_type] }}
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="flex flex-wrap gap-3" v-if="order.status === enums.orderStatusEnum.PENDING">
                    <OnlineOrderReasonComponent />
                    <button type="button" @click="changeStatus(enums.orderStatusEnum.CONFIRMED)"
                        class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#2AC769]">
                        <i class="lab lab-fill-save"></i>
                        <span class="text-sm capitalize text-white">{{ $t('button.accept') }}</span>
                    </button>
                </div>

                <div class="flex flex-wrap gap-3"
                    v-else-if="order.status !== enums.orderStatusEnum.REJECTED && order.status !== enums.orderStatusEnum.CANCELED">
                    <div class="relative">
                        <select v-model="payment_status" @change="changePaymentStatus($event)"
                            class="text-sm capitalize appearance-none pl-4 pr-10 h-[38px] rounded border border-primary bg-white text-primary">
                            <option v-for="paymentStatus in enums.paymentStatusObject" :value="paymentStatus.value" :key="paymentStatus.value">
                                {{ paymentStatus.name }}
                            </option>
                        </select>
                        <i
                            class="lab lab-line-chevron-down lab-font-size-16 absolute top-1/2 right-3.5 -translate-y-1/2 text-primary"></i>
                    </div>

                    <div class="relative" v-if="order.order_type === enums.orderTypeEnum.DELIVERY">
                        <select v-model="order_status" @change="orderStatus($event)"
                            class="text-sm capitalize appearance-none pl-4 pr-10 h-[38px] rounded border border-primary bg-white text-primary">
                            <option v-for="orderStatus in enums.orderStatusObject" :key="orderStatus.value" :value="orderStatus.value">
                                {{ orderStatus.name }}
                            </option>
                        </select>
                        <i
                            class="lab lab-line-chevron-down lab-font-size-16 absolute top-1/2 right-3.5 -translate-y-1/2 text-primary"></i>
                    </div>

                    <div class="relative" v-if="order.order_type === enums.orderTypeEnum.PICK_UP">
                        <select v-model="order_status" @change="orderStatus($event)"
                            class="text-sm capitalize appearance-none pl-4 pr-10 h-[38px] rounded border border-primary bg-white text-primary">
                            <option v-for="orderStatus in enums.orderStatusPickupObject" :value="orderStatus.value" :key="orderStatus.value" >
                                {{ orderStatus.name }}
                            </option>
                        </select>
                        <i
                            class="lab lab-line-chevron-down lab-font-size-16 absolute top-1/2 right-3.5 -translate-y-1/2 text-primary"></i>
                    </div>

                    <OnlineOrderReceiptComponent :order="order" :orderProducts="orderProducts" :orderUser="orderUser"
                        :orderAddress="orderAddress" />
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 sm:col-6">
        <div class="row">
            <div class="col-12">
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">{{ $t('label.order_details') }}</h3>
                    </div>
                    <div class="db-card-body">
                        <div class="pl-3"  v-if="orderProducts.length > 0">
                            <div class="mb-3 pb-3 border-b last:mb-0 last:pb-0 last:border-b-0 border-gray-2"
                                v-for="product in orderProducts" :key="product">
                                <div class="flex items-center gap-3 relative">
                                    <h3
                                        class="absolute top-5 ltr:-left-3 rtl:-right-3 text-sm w-[26px] h-[26px] leading-[26px] text-center rounded-full text-white bg-heading">
                                        {{ product.quantity }}</h3>
                                    <img class="w-16 h-16 rounded-lg flex-shrink-0" :src="product.product_image"
                                        alt="thumbnail">
                                    <div class="flex-auto overflow-hidden">
                                        <h4 :class="!product.variation_names ? 'mb-4' : ''"
                                            class="text-sm capitalize whitespace-nowrap overflow-hidden text-ellipsis">
                                            {{ product.product_name }}</h4>
                                        <p class="text-sm overflow-hidden">{{ product.variation_names }}</p>
                                        <div class="flex flex-wrap items-center justify-between gap-4">
                                            <div class="flex items-center gap-8">
                                                <span class="text-sm font-semibold">{{
                                                    product.subtotal_currency_price
                                                    }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12" v-if="order.status === enums.orderStatusEnum.REJECTED">
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">{{ $t('label.reason') }}</h3>
                    </div>
                    <div class="db-card-body">
                        <p>{{ order.reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 sm:col-6">
        <div class="row">
            <div class="col-12">
                <div class="db-card p-1">
                    <ul class="flex flex-col gap-2 p-3 border-b border-dashed border-[#EFF0F6]">
                        <li class="flex items-center justify-between text-heading">
                            <span class="text-sm leading-6 capitalize">{{ $t('label.subtotal') }}</span>
                            <span class="text-sm leading-6 capitalize">{{ order.subtotal_currency_price }}</span>
                        </li>
                        <li class="flex items-center justify-between text-heading">
                            <span class="text-sm leading-6 capitalize">{{ $t('label.tax_fee') }}</span>
                            <span class="text-sm leading-6 capitalize">{{ order.tax_currency_price }}</span>
                        </li>
                        <li class="flex items-center justify-between text-heading">
                            <span class="text-sm leading-6 capitalize">{{ $t('label.discount') }}</span>
                            <span class="text-sm leading-6 capitalize">{{ order.discount_currency_price }}</span>
                        </li>
                        <li class="flex items-center justify-between text-heading">
                            <span class="text-sm leading-6 capitalize">{{ $t('label.shipping_charge') }}</span>
                            <span class="text-sm leading-6 capitalize font-semibold text-[#1AB759]">
                                {{ order.shipping_charge_currency_price }}
                            </span>
                        </li>
                    </ul>
                    <div class="flex items-center justify-between p-3">
                        <h4 class="text-sm leading-6 font-bold capitalize">{{ $t('label.total') }}</h4>
                        <h5 class="text-sm leading-6 font-bold capitalize">
                            {{ order.total_currency_price }}
                        </h5>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div v-if="isInfoAvailable"  class="db-card p-1">
                    <h3 class="db-card-title">Couriere Info({{ courier_name }})</h3>
                    <div class="grid grid-cols-2 gap-3 p-3" v-if="courier_name == 'Steadfast'">
                        <div class="text-sm capitalize font-semibold">Consignment ID:</div>
                        <div class="text-sm capitalize">{{ info.consignment_id }}</div>
                        <div class="text-sm capitalize font-semibold">Invoice:</div>
                        <div class="text-sm capitalize">{{ info.invoice }}</div>
                        <div class="text-sm capitalize font-semibold">Tracking Code:</div>
                        <div class="text-sm capitalize">{{ info.tracking_code }}</div>
                        <div class="text-sm capitalize font-semibold">Recipient Name:</div>
                        <div class="text-sm capitalize">{{ info.recipient_name }}</div>
                        <div class="text-sm capitalize font-semibold">Recipient Phone:</div>
                        <div class="text-sm capitalize">{{ info.recipient_phone }}</div>
                        <div class="text-sm capitalize font-semibold">Recipient Address:</div>
                        <div class="text-sm capitalize">{{ info.recipient_address }}</div>
                        <div class="text-sm capitalize font-semibold">COD Amount:</div>
                        <div class="text-sm capitalize">{{ info.cod_amount }}</div>
                        <div class="text-sm capitalize font-semibold">Status:</div>
                        <div class="text-sm capitalize">{{ info.status }}</div>
                        <div class="text-sm capitalize font-semibold">Note:</div>
                        <div class="text-sm capitalize">{{ info.note }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 p-3" v-if="courier_name == 'Redex'">
                        <div class="text-sm capitalize font-semibold">Customer Name:</div>
                        <div class="text-sm capitalize">{{info.customer_name}}</div>
                        <div class="text-sm capitalize font-semibold">Customer Phone:</div>
                        <div class="text-sm capitalize">{{info.customer_phone}}</div>
                        <div class="text-sm capitalize font-semibold">Customer Address:</div>
                        <div class="text-sm capitalize">{{info.customer_address}}</div>
                        <div class="text-sm capitalize font-semibold">Delivery Area:</div>
                        <div class="text-sm capitalize">{{info.delivery_area}}</div>
                        <div class="text-sm capitalize font-semibold">ID:</div>
                        <div class="text-sm capitalize">{{info.id}}</div>
                        <div class="text-sm capitalize font-semibold">Invoice:</div>
                        <div class="text-sm capitalize">{{info.invoice}}</div>
                        <div class="text-sm capitalize font-semibold">Order ID:</div>
                        <div class="text-sm capitalize">{{ info.order_id }}</div>
                        <div class="text-sm capitalize font-semibold">Tracking ID:</div>
                        <div class="text-sm capitalize">{{ info.tracking_id }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 p-3" v-if="courier_name == 'Pathao'">
                        <div class="text-sm capitalize font-semibold">Customer Name:</div>
                        <div class="text-sm capitalize">{{ info.recipient_name }}</div>

                        <div class="text-sm capitalize font-semibold">Customer Phone:</div>
                        <div class="text-sm capitalize">{{ info.recipient_phone }}</div>

                        <div class="text-sm capitalize font-semibold">Customer Address:</div>
                        <div class="text-sm capitalize">{{ info.recipient_address }}</div>

                        <!-- <div class="text-sm capitalize font-semibold">Delivery Area:</div>
                        <div class="text-sm capitalize">{{ info.recipient_area }}</div>

                        <div class="text-sm capitalize font-semibold">City:</div>
                        <div class="text-sm capitalize">{{ info.recipient_city }}</div>

                        <div class="text-sm capitalize font-semibold">Zone:</div>
                        <div class="text-sm capitalize">{{ info.recipient_zone }}</div>

                        <div class="text-sm capitalize font-semibold">Area:</div>
                        <div class="text-sm capitalize">{{ info.recipient_area }}</div> -->

                        <div class="text-sm capitalize font-semibold">Delivery Fee:</div>
                        <div class="text-sm capitalize">{{ info.delivery_fee }} Tk.</div>

                        <div class="text-sm capitalize font-semibold">Delivery Type:</div>
                        <div class="text-sm capitalize">{{ info.delivery_type }}</div>

                        <div class="text-sm capitalize font-semibold">ID:</div>
                        <div class="text-sm capitalize">{{ info.id }}</div>

                        <div class="text-sm capitalize font-semibold">Item Description:</div>
                        <div class="text-sm capitalize">{{ info.item_description }}</div>

                        <div class="text-sm capitalize font-semibold">Item Quantity:</div>
                        <div class="text-sm capitalize">{{ info.item_quantity }}</div>

                        <div class="text-sm capitalize font-semibold">Item Weight:</div>
                        <div class="text-sm capitalize">{{ info.item_weight }} Kg.</div>

                        <div class="text-sm capitalize font-semibold">Order Status:</div>
                        <div class="text-sm capitalize">{{ info.order_status }}</div>

                        <div class="text-sm capitalize font-semibold">Merchant Order ID:</div>
                        <div class="text-sm capitalize">{{ info.merchant_order_id }}</div>

                        <div class="text-sm capitalize font-semibold">Special Instruction:</div>
                        <div class="text-sm capitalize">{{ info.special_instruction }}</div>

                        <div class="text-sm capitalize font-semibold">Store ID:</div>
                        <div class="text-sm capitalize">{{ info.store_id }}</div>

                    </div>
                </div>
                <div class="db-card" v-else>
                    <!-- Tab Menu -->
                    <div class="flex border-b">
                    <button
                        v-for="(tab, index) in tabs"
                        :key="index"
                        @click="activeTab = tab.key"
                        :class="['px-4 py-2', activeTab === tab.key ? 'bg-orange-500 text-white' : 'bg-gray-200']"

                    >
                        {{ tab.label }}
                    </button>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-4 border rounded-b-lg">
                    <!-- Steadfast Tab -->
                    <div v-if="activeTab === 'steadfast'">
                        <div class="db-card p-4">
                        <h3 class="db-card-title">Send To SteadFast</h3>
                        <button type="button" @click="sendCourier('Steadfast')"
                            class="flex items-center justify-center text-white px-4 h-[38px] rounded shadow-db-card bg-[#ff6912]">
                            Send
                        </button>
                        </div>
                    </div>

                    <!-- RedX Tab -->
                    <div v-if="activeTab === 'redx'">
                        <div class="db-card p-4">
                        <h3 class="db-card-title">Send To RedX </h3>
                        <div class="mt-2 flex flex-col gap-3">
                            <select  class="border px-4 py-2 rounded" v-model="redx_area_id">
                            <option value="" selected>Select Area</option>
                            <option v-for="area in areas" :key="area.id" :value="area.id">
                                {{ area.name }}
                            </option>
                            </select>
                            <input type="text" v-model="weight" class="border px-4 py-2 rounded" placeholder="Product Weight [Kg]">
                            <button type="button" @click="sendCourier('Redex')" class="bg-[#ff6912] text-white px-4 h-[38px] rounded shadow-db-card">
                            Send
                            </button>
                        </div>
                        </div>
                    </div>

                    <!-- Pathao Tab -->
                    <div v-if="activeTab === 'pathao'">
                        <div class="db-card p-4">
                        <h3 class="db-card-title">Send To Pathao</h3>
                        <div class="mt-2 flex flex-col gap-3">
                            <select v-model="pathao_city_id" class="border px-4 py-2 rounded">
                            <option value="" selected>Select City Name</option>
                            <option v-for="city in cities" :key="city.id" :value="city.city_id">
                                {{ city.city_name }}
                            </option>
                            </select>
                            <select class="border px-4 py-2 rounded" v-model="pathao_zone_id">
                            <option value="" selected>Select Zone Name</option>
                            <option v-for="zone in zones" :key="zone.id" :value="zone.zone_id">
                                {{ zone.zone_name }}
                            </option>
                            </select>
                            <select class="border px-4 py-2 rounded" v-model="pathao_area_id">
                            <option value="" selected>Select Area Name</option>
                            <option v-for="pataho_area in pataho_areas" :key="pataho_area.id" :value="pataho_area.area_id">
                                {{ pataho_area.area_name }}
                            </option>
                            </select>
                            <input type="text" v-model="weight" class="border px-4 py-2 rounded" placeholder="Product Weight [Kg]">

                            <button type="button" @click="sendPathaoCourier" class="bg-[#ff6912] text-white px-4 h-[38px] rounded shadow-db-card">
                            Send
                            </button>
                        </div>
                        </div>
                    </div>

                    </div>
                </div>
            </div>
            <div class="col-12" v-if="order.order_type === enums.orderTypeEnum.DELIVERY && orderAddress.length > 0"
                v-for="address in orderAddress" :key="address">
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title" v-if="address.address_type === enums.addressTypeEnum.SHIPPING">
                            {{ $t('label.shipping_address') }}
                        </h3>
                        <h3 class="db-card-title" v-if="address.address_type === enums.addressTypeEnum.BILLING">
                            {{ $t('label.billing_address') }}
                        </h3>
                    </div>
                    <div class="db-card-body">
                        <div class="flex items-center gap-3 mb-4">
                            <img class="w-8 rounded-full" alt="avatar" :src="orderUser.image">
                            <h4 class="font-semibold text-sm capitalize text-[#374151]">
                                {{ textShortener(address.full_name, 20) }}
                            </h4>
                        </div>
                        <ul class="flex flex-col gap-3 py-4 border-t border-[#EFF0F6]">
                            <li v-if="address.email" class="flex items-center gap-2.5">
                                <i class="lab lab-line-mail lab-font-size-14"></i>
                                <span class="text-xs">{{ address.email }}</span>
                            </li>
                            <li class="flex items-center gap-2.5" v-if="address.phone">
                                <i class="lab lab-line-call-calling lab-font-size-14"></i>
                                <span class="text-xs" dir="ltr">{{ address.country_code + '' + address.phone }}</span>
                            </li>

                            <li class="flex items-center gap-2.5">
                                <i class="lab lab-line-location lab-font-size-14"></i>
                                <span class="text-xs">
                                    <span v-if="address.address">{{ address.address }},</span>
                                    <span v-if="address.city">{{ address.city }},</span>
                                    <span v-if="address.state">{{ address.state }},</span>
                                    <span v-if="address.country">{{ address.country }},</span>
                                    <span v-if="address.zip_code">{{ address.zip_code }}</span>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-12" v-else-if="order.order_type === enums.orderTypeEnum.PICK_UP">
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">
                            {{ $t('label.pick_up_address') }}
                        </h3>
                    </div>

                    <div class="db-card-body">
                        <div class="flex items-center gap-3 mb-4">
                            <img class="w-8 rounded-full" alt="avatar" :src="orderUser.image">
                            <h4 class="font-semibold text-sm capitalize text-[#374151]">
                                {{ textShortener(orderUser.name, 20) }}
                            </h4>
                        </div>
                        <ul class="flex flex-col gap-3 py-4 border-t border-[#EFF0F6]">
                            <li v-if="orderUser.email" class="flex items-center gap-2.5">
                                <i class="lab lab-line-mail lab-font-size-14"></i>
                                <span class="text-xs">{{ orderUser.email }}</span>
                            </li>
                            <li v-if="orderUser.phone" class="flex items-center gap-2.5">
                                <i class="lab lab-line-call-calling lab-font-size-14"></i>
                                <span class="text-xs" dir="ltr">{{ orderUser.country_code + '' + orderUser.phone
                                    }}</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i class="lab lab-line-location lab-font-size-14"></i>
                                <span class="text-xs">
                                    <span v-if="outletAddress.address">{{ outletAddress.address }},</span>
                                    <span v-if="outletAddress.city">{{ outletAddress.city }},</span>
                                    <span v-if="outletAddress.state">{{ outletAddress.state }},</span>
                                    <span v-if="outletAddress.country">{{ outletAddress.country }},</span>
                                    <span v-if="outletAddress.zip_code">{{ outletAddress.zip_code }}</span>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import appService from "../../../services/appService";
import paymentStatusEnum from "../../../enums/modules/paymentStatusEnum";
import addressTypeEnum from "../../../enums/modules/addressTypeEnum";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";
import alertService from "../../../services/alertService";
import OnlineOrderReasonComponent from "./OnlineOrderReasonComponent";
import OnlineOrderReceiptComponent from "./OnlineOrderReceiptComponent";
import axios from "axios";
import { debounce } from 'lodash';
import { info } from "autoprefixer";

export default {
    name: "OnlineOrderShowComponent",
    components: {
        OnlineOrderReceiptComponent,
        LoadingComponent,
        OnlineOrderReasonComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            payment_status: null,
            delivery_boy: null,
            order_status: null,
            selectedCourier: '',
            access_token: '',
            token_type: '',
            redx_area_id: '',
            redx_area_name: '',
            cities: [],
            city_name: '',
            zones: [],
            zone_name: '',
            pathao_areas: [],
            pathao_area_name: '',
            areas: [],
            pataho_areas: [],
            redex_area_name: '',
            selectedArea :'',
            weight: '',
            info: [],
            courier_name: '',
            pathao_city_id: '',
            pathao_zone_id: '',
            pathao_area_id: '',
            amount_taka:'',
            tabs: [
                { key: 'steadfast', label: 'Steadfast' },
                { key: 'redx', label: 'RedX' },
                { key: 'pathao', label: 'Pathao' },
            ],
            activeTab: 'steadfast',
            enums: {
                paymentStatusEnum: paymentStatusEnum,
                addressTypeEnum: addressTypeEnum,
                paymentStatusEnumArray: {
                    [paymentStatusEnum.PAID]: this.$t("label.paid"),
                    [paymentStatusEnum.UNPAID]: this.$t("label.unpaid")
                },
                paymentStatusObject: [
                    {
                        name: this.$t("label.paid"),
                        value: paymentStatusEnum.PAID
                    },
                    {
                        name: this.$t("label.unpaid"),
                        value: paymentStatusEnum.UNPAID,
                    },
                ],
                orderStatusEnum: orderStatusEnum,
                orderStatusEnumArray: {
                    [orderStatusEnum.PENDING]: this.$t("label.pending"),
                    [orderStatusEnum.CONFIRMED]: this.$t("label.confirmed"),
                    [orderStatusEnum.ON_THE_WAY]: this.$t("label.on_the_way"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                    [orderStatusEnum.CANCELED]: this.$t("label.canceled"),
                    [orderStatusEnum.REJECTED]: this.$t("label.rejected"),
                },
                orderStatusObject: [
                    {
                        name: this.$t("label.confirmed"),
                        value: orderStatusEnum.CONFIRMED,
                    },
                    {
                        name: this.$t("label.on_the_way"),
                        value: orderStatusEnum.ON_THE_WAY,
                    },
                    {
                        name: this.$t("label.delivered"),
                        value: orderStatusEnum.DELIVERED,
                    },
                ],
                orderStatusPickupObject: [
                    {
                        name: this.$t("label.confirmed"),
                        value: orderStatusEnum.CONFIRMED,
                    },
                    {
                        name: this.$t("label.delivered"),
                        value: orderStatusEnum.DELIVERED,
                    },
                ],
                orderTypeEnum: orderTypeEnum,
                orderTypeEnumArray: {
                    [orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
                    [orderTypeEnum.PICK_UP]: this.$t("label.pick_up")
                },
            }
        }
    },
    created() {
        this.fetchCitys();
        this.fetchAreass();
        this.fetchAreas();
        this.checkCourierStatus();
    },
    computed: {
        order: function () {
            return this.$store.getters['onlineOrder/show'];
        },
        orderProducts: function () {
            return this.$store.getters['onlineOrder/orderProducts'];
        },
        orderUser: function () {
            return this.$store.getters['onlineOrder/orderUser'];
        },
        orderAddress: function () {
            return this.$store.getters['onlineOrder/orderAddress'];
        },
        outletAddress: function () {
            return this.$store.getters['onlineOrder/outletAddress'];
        },
        selectedAreaName: function() {
            const selectedArea = this.areas.find(area => area.id === this.redx_area_id);
            return selectedArea ? selectedArea.name : '';
        },
        isInfoAvailable() {
            return this.checkInfoAvailability();
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.checkCourierStatus();
        this.$store.dispatch('onlineOrder/show', this.$route.params.id).then(res => {
            this.payment_status = res.data.data.payment_status;
            this.order_status = res.data.data.status;
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        orderStatusClass: function (status) {
            return appService.orderStatusClass(status);
        },
        fetchAreas() {
            axios.get("get_area_list")
                .then(response => {
                    this.areas = response.data.areas;
                })
                .catch(error => {
                    alertService.error(error.message)
                });
        },
        checkCourierStatus() {
            axios.get("admin/online-order/checkCourierStatus/" + this.$route.params.id)
            .then(response => {
                this.info = response.data.data;
                this.courier_name = response.data.courier_name;
                // console.log(this.info);
            })
            .catch(error => {
                alertService.error(error.message);
            });
        },
        checkInfoAvailability() {
            if (this.courier_name != 'Pathao') {
                return this.info.invoice !== null && this.info.invoice !== undefined && this.info.invoice !== "";
            }
            if (this.courier_name == 'Pathao') {
                return this.info.merchant_order_id !== null && this.info.merchant_order_id !== undefined && this.info.merchant_order_id !== "";
            }
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        changeStatus: function (status) {
            appService.acceptOrder().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch("onlineOrder/changeStatus", {
                        id: this.$route.params.id,
                        status: status,
                    }).then((res) => {
                        this.order_status = res.data.data.status;
                        this.loading.isActive = false;
                        alertService.successFlip(
                            1,
                            this.$t("label.status")
                        );
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        changePaymentStatus: function (e) {
            try {
                this.loading.isActive = true;
                this.$store.dispatch("onlineOrder/changePaymentStatus", {
                    id: this.$route.params.id,
                    payment_status: e.target.value,
                }).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(
                        1,
                        this.$t("label.status")
                    );
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            }
        },
        orderStatus: function (e) {
            try {
                this.loading.isActive = true;
                this.$store.dispatch("onlineOrder/changeStatus", {
                    id: this.$route.params.id,
                    status: e.target.value,
                }).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(
                        1,
                        this.$t("label.status")
                    );
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            }
        },
        fetchCitys() {
            this.loading.isActive = true;
            axios.get("pathao/cities")
                .then(response => {
                    this.cities = response.data.data.data;
                    this.loading.isActive = false;

            })
            .catch(error => {
                alertService.error(error.message);
                this.loading.isActive = false;

            });
        },
        fetchZones: debounce(function () {
            if (!this.pathao_city_id) return;
            this.loading.isActive = true;

            axios.get(`pathao/cities/${this.pathao_city_id}/zones`)
                .then(response => {
                    this.zones = response.data.data.data;
                    this.loading.isActive = false;

            })
            .catch(error => {
                alertService.error(error.message);
                    this.loading.isActive = false;

            });
        }, 300),
        fetchAreass: debounce(function () {
            if (!this.pathao_zone_id) return;
                this.loading.isActive = true;
                axios.get(`pathao/zones/${this.pathao_zone_id}/areas`)
                    .then(response => {
                        this.pataho_areas = response.data.data.data;
                        this.loading.isActive = false;

                    })
                    .catch(error => {
                        alertService.error(error.message);
                        this.loading.isActive = false;

                    });
        }, 300),
        sendCourier(courier_name) {
            this.loading.isActive = true;
            let payload = {
                courier: courier_name,
                id: this.$route.params.id,
            };
            if (courier_name !== 'Steadfast') {
                payload.area_id = this.redx_area_id;
                payload.area_name = this.selectedAreaName;
                payload.weight = this.weight;
            }
            this.loading.isActive = true; // Start loading
            axios.post("admin/online-order/sendCourier", payload)
                .then(response => {
                    this.loading.isActive = false;
                    response.data.status ? alertService.success(response.data.message): '';
                    window.location.reload();
            })
            .catch(error => {
                this.loading.isActive = false;
                alertService.error(error.message);
            });
        },
        sendPathaoCourier() {
            // console.log(this.order); return false;
            if (this.pathao_city_id == '') {
                alertService.error('City Required');
                return false;
            }
            if (this.pathao_zone_id == '') {
                  alertService.error('Zone Required');
                  return false;
            }
            if (this.pathao_area_id == '') {
                alertService.error('Area Required');
                return false;
            }

            // order.total_currency_price
            this.loading.isActive = true;
            let payload = {
                store_id: '148064',
                merchant_order_id: this.order.order_serial_no,
                recipient_name: this.orderAddress[0].full_name,
                recipient_phone: '0'+this.orderAddress[0].phone,
                recipient_address: this.orderAddress[0].address+','+this.orderAddress[0].city+this.orderAddress[0].state+','+this.orderAddress[0].country,
                recipient_city: this.pathao_city_id,
                recipient_zone: this.pathao_zone_id,
                recipient_area: this.pathao_area_id,
                delivery_type: 48,
                item_type: 2,
                special_instruction: "Need to Delivery before 5 PM",
                item_quantity: 1,
                item_weight: this.weight,
                item_description: "this is a Cloth item, price- 3000",
                amount_to_collect: this.enums.paymentStatusEnumArray[this.order.payment_status]==='Unpaid' ? parseFloat(this.order.total_amount_price,10) :0,
            };
            // console.log(payload); return false;
            this.loading.isActive = true; // Start loading
            axios.post("pathao/create-order", payload)
                .then(response => {
                this.loading.isActive = false;
                response.data.status
                    ? alertService.success(response.data.message)
                    : alertService.error(response.data.message);
                    window.location.reload();
            })
            .catch(error => {
                this.loading.isActive = false;
                alertService.error(error.message);
            });
        }
    },
    watch: {
        pathao_city_id(newCityId) {
            if (newCityId) {
                this.fetchZones();  // Fetch zones when city is selected
            }
        },
        pathao_zone_id(newZoneId) {
            if (newZoneId) {
                this.fetchAreass();  // Fetch areas when zone is selected
            }
        },
    }
}
</script>
