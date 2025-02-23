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
                            <label class="db-field-title">Steadfast Base URL</label>
                            <input type="text" v-model="steadfast.base_url" class="db-field-control" />
                        </div>
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
                            <label class="db-field-title">Redex Sandbox</label>
                            <select v-model="redex.sandbox" class="db-field-control">
                                <option value="true">True</option>
                                <option value="false">False</option>
                            </select>
                        </div>
                        <div class="form-col-12">
                            <label class="db-field-title">Redex Access Token</label>
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
                            <label class="db-field-title">Pathao Base URL</label>
                            <input type="text" v-model="pathao.base_url" class="db-field-control" />
                        </div>
                        <div class="form-col-12">
                            <label class="db-field-title">Pathao Client ID</label>
                            <input type="text" v-model="pathao.client_id" class="db-field-control" />
                        </div>
                        <div class="form-col-12">
                            <label class="db-field-title">Pathao Client Secret</label>
                            <input type="text" v-model="pathao.client_secret" class="db-field-control" />
                        </div>
                        <div class="form-col-12">
                            <label class="db-field-title">Pathao Username</label>
                            <input type="text" v-model="pathao.username" class="db-field-control" />
                        </div>
                        <div class="form-col-12">
                            <label class="db-field-title">Pathao Password</label>
                            <input type="text" v-model="pathao.password" class="db-field-control" />
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
            steadfast: {
                base_url: "",
                api_key: "",
                secret_key: ""
            },
            redex: {
                sandbox: "",
                access_token: ""
            },
            pathao: {
                base_url: "",
                client_id: "",
                client_secret: "",
                username: "",
                password: ""
            }
        };
    },
    mounted() {
        setTimeout(() => {
            this.fetchCouriers();
        }, 1000);
    },
    methods: {
        save(courier) {
            this.loading.isActive = true;
            axios.post('/settings/courier/insert', {
                name: courier,
                base_url: this[courier].base_url,
                api_key: this[courier].api_key,
                secret_key: this[courier].secret_key,
                sandbox: this[courier].sandbox,
                access_token: this[courier].access_token,
                client_id: this[courier].client_id,
                client_secret: this[courier].client_secret,
                username: this[courier].username,
                password: this[courier].password,
            })
            .then(response => {
                this.loading.isActive = false;
                alertService.success(response.config.method === "put" ? 'Updated' : 'Saved');
            })
            .catch(error => {
                this.loading.isActive = false;
                alertService.error(error.message);
            });
        }
    },
    computed: {
       fetchCouriers() {
            this.loading.isActive = true;
            axios.get('/settings/couriers') // Update this URL to your API endpoint
                .then(response => {
                    const data = response.data;
                    
                    this.steadfast.base_url = data.steadfast.base_url;
                    this.steadfast.api_key = data.steadfast.api_key;
                    this.steadfast.secret_key = data.steadfast.secret_key;

                    this.redex.sandbox = data.redex.sandbox;
                    this.redex.access_token = data.redex.access_token;

                    this.pathao.base_url = data.pathao.base_url;
                    this.pathao.client_id = data.pathao.client_id;
                    this.pathao.client_secret = data.pathao.client_secret;
                    this.pathao.username = data.pathao.username;
                    this.pathao.password = data.pathao.password;

                    this.loading.isActive = false;
                })
                .catch(error => {
                    console.error("Fetch Error:", error); // Debugging
                    alertService.error(error.message);
                    this.loading.isActive = false;
                });
        },
    }
};
</script>

