<template>
    <LoadingComponent :props="loading" />
    <div id="courier" class="db-tab-div active">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 mb-5">
            <button v-for="(courier, index) in couriers" :key="courier" @click="selectActive(index)"
                class="db-tab-sub-btn w-full flex items-center gap-3 h-10 px-4 rounded-lg transition"
                :class="index === selectIndex ? 'bg-primary text-white' : 'bg-white hover:text-primary hover:bg-primary/5'">
                <span class="capitalize whitespace-nowrap text-[15px]">{{ courier }}</span>
            </button>
        </div>
        <div v-for="(courier, index) in couriers" :key="courier" class="db-card db-tab-sub-div"
            :class="index === selectIndex ? 'active' : 'hidden'">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ courier }}</h3>
            </div>
            <div class="db-card-body">
                <form @submit.prevent="save(index)" :id="'formElem' + index" class="w-full d-block">
                    <div class="form-row">
                        <input type="hidden" :value="courier" name="courier">
                        <div class="form-col-12">
                            <label :for="'api_key' + index" class="db-field-title">API Key</label>
                            <input type="text" v-model="courierInputs[index].api_key" :id="'api_key' + index"
                                class="db-field-control" />
                        </div>
                        <div class="form-col-12">
                            <label :for="'secret_key' + index" class="db-field-title">Secret Key</label>
                            <input type="text" v-model="courierInputs[index].secret_key" :id="'secret_key' + index"
                                class="db-field-control" />
                        </div>
                        <div class="form-col-12 mt-3">
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
import alertService from "../../../../services/alertService";
import axios from "axios";

export default {
    name: "CourierComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            selectIndex: 0,
            couriers: [
                "Stedfast",
                "Redex",
                "Pathao",
            ],
            courierInputs: [
                { api_key: "", secret_key: "" },
                { api_key: "", secret_key: "" },
                { api_key: "", secret_key: "" }
            ]
        };
    },
    methods: {
        selectActive(index) {
            this.selectIndex = index;
        },
        save(index) {
            this.loading.isActive = true;
            axios.post("admin/setting/courier/insert", {
                type: "courier",
                value: this.courierInputs[index],
                key: this.couriers[index]
            }).then((response) => {
                this.loading.isActive = false;
                if (response.data.success) {
                    alertService.success(response.data.message);
                } else if (response.data.errors) {
                    alertService.error(response.data.errors);
                } else {
                    alertService.error(response.data.message);
                }
            }).catch(error => {
                this.loading.isActive = false;
                if (error.response.data.errors) {
                    alertService.error(error.response.data.errors);
                } else {
                    alertService.error(error.response.data.message);
                }
            });
        }
    }
};
</script>

