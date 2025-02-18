<template>
    <LoadingComponent :props="loading" />
    <div id="courier" class="db-tab-div active">
        <div class="grid sm:grid-cols-3 gap-3 mb-5">
            <button @click="selectIndex = 'steadfast'" class="db-tab-sub-btn w-full flex items-center gap-3 h-10 px-4 rounded-lg transition"
                :class="selectIndex === 'steadfast' ? 'bg-primary text-white' : 'bg-white hover:text-primary hover:bg-primary/5'">
                <span class="capitalize whitespace-nowrap text-[15px]">Steadfast</span>
            </button>
            <button @click="selectIndex = 'redex'" class="db-tab-sub-btn w-full flex items-center gap-3 h-10 px-4 rounded-lg transition"
                :class="selectIndex === 'redex' ? 'bg-primary text-white' : 'bg-white hover:text-primary hover:bg-primary/5'">
                <span class="capitalize whitespace-nowrap text-[15px]">Redex</span>
            </button>
            <button @click="selectIndex = 'pathao'" class="db-tab-sub-btn w-full flex items-center gap-3 h-10 px-4 rounded-lg transition"
                :class="selectIndex === 'pathao' ? 'bg-primary text-white' : 'bg-white hover:text-primary hover:bg-primary/5'">
                <span class="capitalize whitespace-nowrap text-[15px]">Pathao</span>
            </button>
        </div>

        <div v-if="selectIndex === 'steadfast'" class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">Steadfast</h3>
            </div>
            <div class="db-card-body">
                <form @submit.prevent="save('steadfast')" class="w-full">
                    <div class="form-row">
                        <div class="form-col-12">
                            <label class="db-field-title">Steadfast API Key</label>
                            <input type="text" v-model="steadfast.api_key" class="db-field-control" />
                        </div>
                        <div class="form-col-12">
                            <label class="db-field-title">Steadfast Secret Key</label>
                            <input type="text" v-model="steadfast.secret_key" class="db-field-control" />
                        </div>
                        <div class="form-col-12 mt-3">
                            <button type="submit" class="db-btn text-white bg-primary">
                                <i class="lab lab-fill-save"></i>
                                <span>Save</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="selectIndex === 'redex'" class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">Redex</h3>
            </div>
            <div class="db-card-body">
                <form @submit.prevent="save('redex')" class="w-full">
                    <div class="form-row">
                        <div class="form-col-12">
                            <label class="db-field-title">Redex API Key</label>
                            <select v-model="redex.sandbox" class="db-field-control">
                                <option value="true">True</option>
                                <option value="false">False</option>
                            </select>
                        </div>
                        <div class="form-col-12">
                            <label class="db-field-title">Redex Secret Key</label>
                            <input type="text" v-model="redex.access_token" class="db-field-control" />
                        </div>
                        <div class="form-col-12 mt-3">
                            <button type="submit" class="db-btn text-white bg-primary">
                                <i class="lab lab-fill-save"></i>
                                <span>Save</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="selectIndex === 'pathao'" class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">Pathao</h3>
            </div>
            <div class="db-card-body">
                <form @submit.prevent="save('pathao')" class="w-full">
                    <div class="form-row">
                        <div class="form-col-12">
                            <label class="db-field-title">Pathao API Key</label>
                            <input type="text" v-model="pathao.api_key" class="db-field-control" />
                        </div>
                        <div class="form-col-12">
                            <label class="db-field-title">Pathao Secret Key</label>
                            <input type="text" v-model="pathao.secret_key" class="db-field-control" />
                        </div>
                        <div class="form-col-12 mt-3">
                            <button type="submit" class="db-btn text-white bg-primary">
                                <i class="lab lab-fill-save"></i>
                                <span>Save</span>
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
            selectIndex: 'steadfast',
            steadfast: { api_key: "", secret_key: "" },
            redex: { sandbox: "", access_token: "" },
            pathao: { api_key: "", secret_key: "" }
        };
    },
    created() {
        this.fetchCouriers();
    },
    methods: {
        fetchCouriers() {
            this.loading.isActive = true;
            axios.get("admin/setting/courier")
                .then(response => {
                    // console.log("Fetched Data:", response.data); // Debugging

                    response.data.forEach(item => {
                        const name = item.name?.toLowerCase(); // Normalize name
                        if (name === 'steadfast') {
                            this.steadfast.api_key = item.api_key || "";
                            this.steadfast.secret_key = item.secret_key || "";
                        } else if (name === 'redex') {
                            this.redex.sandbox = item.sandbox || "";
                            this.redex.access_token = item.access_token || "";
                        } else if (name === 'pathao') {
                            this.pathao.api_key = item.api_key || "";
                            this.pathao.secret_key = item.secret_key || "";
                        }
                    });

                    this.loading.isActive = false;
                })
                .catch(error => {
                    console.error("Fetch Error:", error); // Debugging
                    alertService.error(error.message);
                    this.loading.isActive = false;
                });
        },
        save(courier) {
            this.loading.isActive = true;
            axios.post("admin/setting/courier/insert", {
                type: "courier",
                value: this[courier],
                key: courier
            }).then(response => {
                this.loading.isActive = false;
                if (response.data.success) {
                    alertService.success(response.data.message);
                } else {
                    alertService.error(response.data.message || response.data.errors);
                }
            }).catch(error => {
                this.loading.isActive = false;
                alertService.error(error.response.data.message || error.response.data.errors);
            });
        }
    }
};
</script>
