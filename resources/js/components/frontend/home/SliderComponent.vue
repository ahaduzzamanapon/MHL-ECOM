<template>
    <LoadingComponent :props="loading" />
    <div class="flex flex-col lg:flex-row gap-8 container">
        <!-- Categories Section -->
        <aside class="lg:w-1/4 md:w-1/3 hidden lg:block overflow-y-auto h-[300px] p-4 bg-white rounded-xl shadow-md scrollbar-thin scrollbar-track-rounded scrollbar-thumb-rounded scrollbar-thumb-gray-300 scrollbar-track-gray-100 custom-scrollbar">
            <h2 class="text-center border-b-2 border-[#f23e14] text-lg font-semibold pb-2 mb-4">
                Categories
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-2 gap-4">
                <div v-for="category in categories" :key="category.slug" class="text-center">
                    <router-link :to="{ name: 'frontend.product', query: { category: category.slug } }" class="block rounded-lg shadow-sm hover:shadow-md transition">
                        <img class="w-full rounded-t-lg" :src="category.thumb" :alt="category.name">
                        <span class="block text-sm font-medium py-2 px-3 text-gray-800 truncate group-hover:text-primary">
                            {{ category.name }}
                        </span>
                    </router-link>
                </div>
            </div>
        </aside>
        
        <!-- Slider Section -->
        <main class="lg:w-3/4 w-full">
            <div class="container mx-auto">
                <Swiper v-if="sliders.length > 0" dir="rtl" :slides-per-view="1" :space-between="20" :speed="1000"
                    :loop="true" :navigation="true" :pagination="{ clickable: true }" :autoplay="{ delay: 2500 }"
                    :modules="modules" class="banner-swiper" :breakpoints="{
                        1024: { slidesPerView: 1, spaceBetween: 20 },
                        768: { slidesPerView: 1, spaceBetween: 10 },
                        480: { slidesPerView: 1, spaceBetween: 5 }
                    }">
                    <SwiperSlide v-for="slider in sliders" :key="slider.id">
                        <a v-if="slider.link" :href="slider.link" target="_blank" class="block">
                            <img class="w-full rounded-xl shadow-md" :src="slider.image" :alt="slider.alt || 'banner'">
                        </a>
                        <img v-else class="w-full rounded-xl shadow-md" :src="slider.image" :alt="slider.alt || 'banner'">
                    </SwiperSlide>
                </Swiper>
            </div>
        </main>
    </div>
</template>

<script>
import 'swiper/css';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';
import statusEnum from "../../../enums/modules/statusEnum";
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "SliderComponent",
    components: {
        Swiper,
        SwiperSlide,
        LoadingComponent
    },
    setup() {
        return {
            modules: [Navigation, Pagination, Autoplay],
        };
    },
    data() {
        return {
            loading: { isActive: false },
            sliderProps: {
                search: {
                    paginate: 0,
                    order_column: 'id',
                    order_type: 'desc',
                    status: statusEnum.ACTIVE
                }
            }
        };
    },
    computed: {
        sliders() {
            return this.$store.getters['frontendSlider/lists'];
        },
        categories() {
            return this.$store.getters["frontendProductCategory/lists"];
        }
    },
    mounted() {
        this.loading.isActive = true;
        Promise.all([
            this.$store.dispatch("frontendSlider/lists", this.sliderProps.search),
            this.$store.dispatch("frontendProductCategory/lists", {
                paginate: 0,
                order_column: "id",
                order_type: "asc",
                parent_id: null,
                status: statusEnum.ACTIVE,
            })
        ]).finally(() => {
            this.loading.isActive = false;
        });
    }
};
</script>
<style scoped>
/* Optional: Customize the scrollbar appearance */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 9999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 9999px;
}
</style>
