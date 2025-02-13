<template>
    <LoadingComponent :props="loading" />
    <div id="courier" class="db-tab-div active">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 mb-5">
            <button v-for="(courier, index) in couriers" :key="courier" @click="selectActive(index)"
                class="db-tab-sub-btn w-full flex items-center gap-3 h-10 px-4 rounded-lg transition bg-white"
                :class="index === selectIndex ? 'text-white bg-primary' : 'bg-white hover:text-primary hover:bg-primary/5'">
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
                            <label :for="'input' + index" class="db-field-title">Enter {{ courier }} Data</label>
                            <input type="text" v-model="courierInputs[index]" :id="'input' + index" class="db-field-control" />
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
            courierInputs: ["", "", ""]
        };
    },
    methods: {
        save(index) {
            try {
                this.loading.isActive = true;
                alertService.success(`Saved successfully for ${this.couriers[index]}`);
                this.loading.isActive = false;
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        selectActive(index) {
            this.selectIndex = index;
        }
    }
};
</script>

