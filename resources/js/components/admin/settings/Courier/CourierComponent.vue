<template>
    <LoadingComponent :props="loading" />
    <div id="courier" class="db-tab-div active">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 mb-5">
            <button @click="selectActive(0)"
                class="db-tab-sub-btn w-full flex items-center gap-3 h-10 px-4 rounded-lg transition bg-white hover:text-primary hover:bg-primary/5"
                :data-tab="'#stedfast'">
                <span class="capitalize whitespace-nowrap text-[15px]">Stedfast</span>
            </button>
            <button @click="selectActive(1)"
                class="db-tab-sub-btn w-full flex items-center gap-3 h-10 px-4 rounded-lg transition bg-white hover:text-primary hover:bg-primary/5"
                :data-tab="'#redex'">
                <span class="capitalize whitespace-nowrap text-[15px]">Redex</span>
            </button>
            <button @click="selectActive(2)"
                class="db-tab-sub-btn w-full flex items-center gap-3 h-10 px-4 rounded-lg transition bg-white hover:text-primary hover:bg-primary/5"
                :data-tab="'#pathao'">
                <span class="capitalize whitespace-nowrap text-[15px]">Pathao</span>
            </button>
        </div>
        <div :id="courier" class="db-card db-tab-sub-div" v-for="(courier, index) in couriers" :key="courier" :class="index === selectIndex ? 'active' : ''">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ courier }}</h3>
            </div>
            <div class="db-card-body">
                <form @submit.prevent="save(index)" :id="'formElem' + index" class="w-full d-block">
                    <div class="form-row">
                        <input type="hidden" :value="courier" name="courier">

                        <div class="form-col-12 sm:form-col-6" v-for="courierOption in courierOptions[index]"
                            :key="courierOption">
                            <label :for="courierOption.option" class="db-field-title">
                                {{ $t("label." + courierOption.option) }}
                            </label>
                            <input v-if="courierOption.type === enums.inputTypeEnum.TEXT" type="text"
                                :value="courierOption.value"
                                v-bind:class="errors[courierOption.option] ? 'invalid' : ''"
                                :name="courierOption.option" :id="courierOption.option"
                                class="db-field-control" />

                            <select v-else class="db-field-control" :id="courierOption.option"
                                :name="courierOption.option"
                                v-bind:class="errors[courierOption.option] ? 'invalid' : ''">
                                <option :value="index" :selected="index === courierOption.value"
                                    v-for="(activity, index) in courierOption.activities">
                                    {{ $t("label." + activity) }}
                                </option>
                            </select>

                            <small class="db-field-alert" v-if="errors[courierOption.option]">{{
                                errors[courierOption.option][0]
                            }}</small>
                        </div>
                        <div class="form-col-12">
                            <button type="submit" :id="'formButton' + index" class="db-btn text-white bg-primary">
                                <i class="lab lab-fill-save"></i>
                                <span>{{ $t("button.save") }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import appService from "../../../../services/appService";
import alertService from "../../../../services/alertService";
import inputTypeEnum from "../../../../enums/modules/inputTypeEnum";

export default {
    name: "CourierComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            search: {
                paginate: 0,
                order_column: "id",
                order_type: "asc",
                excepts: "1|2"
            },
            selectIndex: 0,
            enums: {
                inputTypeEnum: inputTypeEnum
            },
            errors: {},
            couriers: [
                "Stedfast",
                "Redex",
                "Pathao",
            ],
            courierOptions: [
                [
                    {
                        option: "stedfast_api_key",
                        value: "",
                        activities: [],
                        type: inputTypeEnum.TEXT,
                    },
                    {
                        option: "stedfast_api_secret",
                        value: "",
                        activities: [],
                        type: inputTypeEnum.TEXT,
                    },
                ],
                [
                    {
                        option: "redex_api_key",
                        value: "",
                        activities: [],
                        type: inputTypeEnum.TEXT,
                    },
                    {
                        option: "redex_api_secret",
                        value: "",
                        activities: [],
                        type: inputTypeEnum.TEXT,
                    },
                ],
                [
                    {
                        option: "pathao_api_key",
                        value: "",
                        activities: [],
                        type: inputTypeEnum.TEXT,
                    },
                    {
                        option: "pathao_api_secret",
                        value: "",
                        activities: [],
                        type: inputTypeEnum.TEXT,
                    },
                ],
            ],

        };
    },
    mounted() {
        try {
            this.loading.isActive = true;
            this.$store.dispatch("paymentGateway/lists", this.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        } catch (err) {
            this.loading.isActive = false;
            alertService.error(err);
        }
    },
    methods: {
        save: function (index) {
            try {
                let form = document.getElementById("formElem" + index);
                let formDataObj = {};
                [...form.elements].filter((el) => el.tagName !== 'BUTTON').forEach((item) => {
                    formDataObj[item.name] = item.value;
                });

                this.loading.isActive = true;
                this.$store.dispatch("paymentGateway/save", { form: formDataObj, search: this.search }).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(res.config.method === "put" ?? 0, this.$t("menu.payment_gateway"));
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        selectActive: function (index) {
            this.selectIndex = index;
            let formButton = document.getElementById("formButton" + index);
            formButton.click();
        }
    }
};
</script>

