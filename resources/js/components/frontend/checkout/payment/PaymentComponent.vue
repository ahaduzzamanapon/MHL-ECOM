<template>
    <LoadingComponent :props="loading" />
    <div class="row">
        <div class="col-12 lg:col-8">
            <div class="mb-6 rounded-2xl shadow-card">
                <h4 class="font-bold capitalize p-4 border-b border-gray-100">
                    {{ $t('label.select_payment_method') }}
                </h4>

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 p-4" style="display: flex;flex-direction: column;">
                    <div v-if="Object.keys(cashOnDelivery).length > 0 && setting.site_cash_on_delivery === ActivityEnum.ENABLE"
                        @click.prevent="selectPaymentMethod(cashOnDelivery)"
                        :class="Object.keys(paymentMethod).length > 0 && cashOnDelivery.id === paymentMethod.id ? 'border-primary/50 bg-[#FFF4F1]' : 'border-white bg-white'"
                        class="flex flex-col items-center justify-center gap-2.5 py-4 rounded-lg shadow-xs cursor-pointer border" style="display: flex;flex-direction: row;justify-content: flex-start;width: 276px;height: 60px;gap: 10px;border-radius: 8px;padding: 10px;">
                        <img class="h-6" :src="cashOnDelivery.image" alt="payment" style="height: 40px;width: 40px;border-radius: 10px;" />
                        <div style="display: flex;flex-direction: column;">
                            <span class="text-xm font-medium" style="font-weight: 600;font-size: 12px;line-height: 14px;letter-spacing: 0px;color: #081B37;">{{ cashOnDelivery.name }}</span>
                            <span class="text-xs" style="font-size: 11px;font-weight: 400;color: #697386;">Pay with {{ cashOnDelivery.name }}</span>
                        </div>
                    </div>
                    <!-- <div v-if="profile.balance >= total" @click.prevent="selectPaymentMethod(credit)"
                        :class="Object.keys(paymentMethod).length > 0 && credit.id === paymentMethod.id ? 'border-primary/50 bg-[#FFF4F1]' : 'border-white bg-white'"
                        class="flex flex-col items-center justify-center gap-2.5 py-4 rounded-lg shadow-xs cursor-pointer border">
                        <img class="h-6" :src="credit.image" alt="payment"  style="height: 40px;width: 40px;border-radius: 10px;"/>
                        <span class="text-xs font-medium">{{ credit.name }} ({{ profile.balance }})</span>
                    </div> -->
                    <div v-if="setting.site_online_payment_gateway === ActivityEnum.ENABLE"
                        v-for="paymentGateway in paymentGateways" @click.prevent="selectPaymentMethod(paymentGateway)"
                        :class="Object.keys(paymentMethod).length > 0 && paymentGateway.id === paymentMethod.id ? 'border-primary/50 bg-[#FFF4F1]' : 'border-white bg-white'"
                        class="flex flex-col items-center justify-center gap-2.5 py-4 rounded-lg shadow-xs cursor-pointer border" style="display: flex;flex-direction: row;justify-content: flex-start;width: 276px;height: 60px;gap: 10px;border-radius: 8px;padding: 10px;">
                        <img class="h-6" :src="paymentGateway.image" alt="payment" style="height: 40px;width: 40px;"  />
                        <div style="display: flex;flex-direction: column;">
                            <span class="text-xm font-medium" style="font-weight: 600;font-size: 12px;line-height: 14px;letter-spacing: 0px;color: #081B37;">{{ paymentGateway.name }}</span>
                            <span class="text-xs" v-if="paymentGateway.name === 'Credit/Debit Card'">
                                <svg width="108" height="15" viewBox="0 0 108 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect y="0.299072" width="24" height="14" rx="2" fill="white"/>
                                <path d="M14.3724 3.84702C15.1004 3.71715 15.8117 3.79881 16.5072 4.04819C16.5265 4.0551 16.5446 4.06588 16.5736 4.08042C16.4791 4.53629 16.3862 4.98286 16.2924 5.43589C15.927 5.27154 15.5545 5.16419 15.1625 5.13999C14.8727 5.12208 14.5843 5.12504 14.3187 5.26792C14.2461 5.30699 14.1764 5.36154 14.1205 5.42319C13.9667 5.59269 13.9577 5.82359 14.1244 5.97788C14.2812 6.12276 14.4641 6.24114 14.6449 6.35483C14.9192 6.52731 15.2085 6.6748 15.4799 6.8519C16.7108 7.65534 16.3392 9.18624 15.6 9.80405C15.1626 10.1695 14.6594 10.361 14.1136 10.4369C13.319 10.5473 12.5389 10.4647 11.7797 10.1927C11.7409 10.1788 11.7034 10.1606 11.6576 10.141C11.7545 9.6728 11.8506 9.21005 11.9506 8.7269C12.0159 8.75775 12.0748 8.78475 12.1332 8.81284C12.6589 9.0658 13.2104 9.17821 13.7894 9.12339C13.9994 9.10345 14.2004 9.04484 14.3724 8.91049C14.6452 8.69724 14.6746 8.34947 14.4135 8.12339C14.2331 7.96747 14.0162 7.85546 13.8148 7.72592C13.5179 7.53498 13.1986 7.37345 12.9261 7.15073C11.9948 6.38915 12.2833 5.21277 12.8353 4.62534C13.2582 4.17534 13.788 3.95131 14.3724 3.84702ZM9.48767 3.90756C9.50393 3.90765 9.5206 3.91202 9.55212 3.91635C9.50352 4.04039 9.45815 4.15914 9.41052 4.27671C8.60165 6.27474 7.79149 8.2729 6.98474 10.2718C6.95036 10.3568 6.90767 10.3869 6.81873 10.3861C6.32954 10.3818 5.84013 10.3811 5.35095 10.3861C5.2554 10.387 5.21357 10.3608 5.18884 10.2611C4.77222 8.58144 4.34806 6.90379 3.93591 5.22299C3.86109 4.91784 3.72025 4.68779 3.43005 4.58335C2.98535 4.4233 2.53636 4.27503 2.08923 4.12241C2.04626 4.10778 2.0015 4.09867 1.94763 4.08432C1.95928 4.02445 1.96854 3.97805 1.97791 3.93198C1.97874 3.92847 1.9821 3.92492 1.99353 3.91049C2.03305 3.90868 2.08301 3.90464 2.1322 3.90464C2.98931 3.90448 3.84642 3.90449 4.70349 3.90561C5.12773 3.90619 5.40506 4.13696 5.48572 4.57163C5.70542 5.758 5.92037 6.9462 6.13708 8.13315C6.14526 8.17775 6.15691 8.22205 6.17615 8.30503C6.27198 8.05559 6.35482 7.84092 6.43689 7.62631C6.89876 6.4187 7.36072 5.21029 7.82166 4.00229C7.84458 3.94227 7.868 3.90317 7.94373 3.90366C8.45811 3.90813 8.97325 3.90664 9.48767 3.90756ZM20.5912 3.91049C20.6696 3.90999 20.704 3.92864 20.722 4.01889C20.9999 5.40841 21.2834 6.79714 21.5648 8.18589C21.7034 8.86955 21.8405 9.55371 21.9779 10.2376C21.9853 10.2751 21.9906 10.313 22.0004 10.3753C21.8097 10.3753 21.6387 10.3752 21.4681 10.3753C21.1879 10.3756 20.9075 10.3736 20.6273 10.3783C20.5446 10.3796 20.5089 10.3488 20.4935 10.266C20.4473 10.0162 20.3909 9.76807 20.347 9.51792C20.3318 9.43129 20.2964 9.4062 20.2142 9.40659C19.5912 9.40967 18.9681 9.40985 18.3451 9.40561C18.2554 9.40499 18.222 9.44403 18.1957 9.5228C18.11 9.77949 18.0207 10.0351 17.9261 10.2884C17.912 10.3262 17.8614 10.375 17.8275 10.3753C17.2929 10.3808 16.7582 10.3792 16.2045 10.3792C16.3203 10.0929 16.4302 9.82042 16.5404 9.54819C17.2236 7.86101 17.9076 6.17412 18.5902 4.48667C18.7669 4.04975 18.9646 3.91076 19.4242 3.91049C19.8131 3.91034 20.2023 3.91307 20.5912 3.91049ZM10.5404 10.3744H8.92322C9.36901 8.22149 9.81367 6.07234 10.2601 3.91635H11.8744C11.429 6.07203 10.9857 8.2191 10.5404 10.3744ZM18.7006 8.08725H20.0551C19.8929 7.28195 19.7325 6.48763 19.5726 5.6937C19.5619 5.6926 19.5511 5.69187 19.5404 5.69077C19.2623 6.48485 18.9836 7.27918 18.7006 8.08725Z" fill="#1B5BA6"/>
                                <rect x="28" y="0.299072" width="24" height="14" rx="2" fill="white"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M37.0767 11.2227H42.0615V3.23315H37.0767V11.2227Z" fill="#FF5F00"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M37.5901 7.22871C37.5888 5.66989 38.3181 4.19717 39.5677 3.23464C37.3194 1.50083 34.0644 1.88372 32.2974 4.08991C30.5306 6.29624 30.9208 9.49036 33.1692 11.2242C35.0468 12.6722 37.6901 12.6722 39.5677 11.2242C38.3177 10.2615 37.5884 8.78794 37.5901 7.22871Z" fill="#EB001B"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M48.0646 12.8973V11.8201H47.9407L47.7957 12.5895L47.6507 11.8201H47.526V12.8973H47.6158V12.0817L47.7508 12.7818H47.8437L47.9783 12.0817V12.8973H48.0646ZM47.2997 12.8973V12.0048H47.526V11.8201H46.9874V12.0048H47.2006V12.8973H47.2997Z" fill="#F79E1B"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M47.9454 7.2285C47.9453 10.0345 45.6271 12.3093 42.7677 12.3093C41.607 12.3091 40.4802 11.9265 39.5677 11.2226C41.816 9.4886 42.2063 6.29462 40.4393 4.08829C40.1845 3.77018 39.8919 3.48297 39.5677 3.23303C41.8157 1.49879 45.0707 1.88111 46.8381 4.08702C47.5554 4.98237 47.9453 6.08815 47.9454 7.22709V7.2285Z" fill="#F79E1B"/>
                                <rect x="56" y="0.299316" width="24" height="14" fill="white"/>
                                <path d="M61.1126 0.299316H66.3541C67.0857 0.299316 67.5408 0.934913 67.3701 1.71729L64.9298 12.8835C64.7576 13.6632 64.025 14.2993 63.2929 14.2993H58.0519C57.3212 14.2993 56.8651 13.6632 57.0358 12.8835L59.4771 1.71729C59.6478 0.934913 60.38 0.299316 61.1126 0.299316Z" fill="#E21836"/>
                                <path d="M65.9179 0.299316H71.9455C72.677 0.299316 72.3472 0.934913 72.1751 1.71729L69.7352 12.8835C69.564 13.6632 69.6174 14.2993 68.8843 14.2993H62.8567C62.1236 14.2993 61.6699 13.6632 61.8422 12.8835L64.2819 1.71729C64.4552 0.934913 65.1858 0.299316 65.9179 0.299316Z" fill="#00447C"/>
                                <path d="M71.7065 0.299316H76.948C77.6807 0.299316 78.1357 0.934913 77.9636 1.71729L75.5237 12.8835C75.3515 13.6632 74.6184 14.2993 73.8859 14.2993H68.6468C67.9137 14.2993 67.459 13.6632 67.6307 12.8835L70.071 1.71729C70.2417 0.934913 70.9734 0.299316 71.7065 0.299316Z" fill="#007B84"/>
                                <path d="M62.4814 3.87763C61.9424 3.88348 61.7832 3.87763 61.7323 3.86483C61.7128 3.96382 61.3491 5.75201 61.348 5.75351C61.2697 6.11528 61.2128 6.37317 61.0192 6.5397C60.9093 6.63651 60.7811 6.68321 60.6324 6.68321C60.3933 6.68321 60.2541 6.55672 60.2307 6.3168L60.2262 6.23442C60.2262 6.23442 60.299 5.74983 60.299 5.74711C60.299 5.74711 60.6808 4.11754 60.7491 3.90213C60.7527 3.88988 60.7537 3.88348 60.7546 3.87763C60.0116 3.88457 59.8799 3.87763 59.8708 3.86483C59.8658 3.88239 59.8474 3.98342 59.8474 3.98342L59.4576 5.82009L59.4241 5.97586L59.3594 6.48537C59.3594 6.63651 59.3872 6.75987 59.4427 6.86417C59.6203 7.1949 60.1268 7.24446 60.4134 7.24446C60.7826 7.24446 61.1289 7.16086 61.363 7.00822C61.7693 6.75238 61.8756 6.35248 61.9704 5.9971L62.0143 5.81478C62.0143 5.81478 62.4075 4.12231 62.4744 3.90213C62.4769 3.88988 62.4779 3.88348 62.4814 3.87763ZM63.8194 5.24296C63.7246 5.24296 63.5513 5.26747 63.3957 5.34876C63.3392 5.37966 63.2858 5.41534 63.2295 5.45088L63.2803 5.25521L63.2525 5.22226C62.9225 5.29347 62.8487 5.30301 62.5438 5.34876L62.5183 5.36687C62.4829 5.67963 62.4515 5.91477 62.3202 6.52954C62.2703 6.75611 62.2184 6.98486 62.1664 7.21088L62.1805 7.23961C62.4928 7.22205 62.5876 7.22205 62.8591 7.22681L62.8811 7.20135C62.9156 7.01304 62.9201 6.96893 62.9965 6.58754C63.0324 6.40672 63.1072 6.00941 63.1442 5.86794C63.212 5.83444 63.2789 5.80149 63.3428 5.80149C63.495 5.80149 63.4765 5.94296 63.4706 5.99933C63.4641 6.09396 63.4086 6.40304 63.3518 6.66842L63.3138 6.83971C63.2874 6.9662 63.2584 7.08916 63.2319 7.21456L63.2434 7.23961C63.5513 7.22205 63.6452 7.22205 63.9081 7.22681L63.9391 7.20135C63.9866 6.90725 64.0005 6.82855 64.0848 6.40032L64.1273 6.20357C64.2097 5.81851 64.251 5.62326 64.1887 5.46422C64.1228 5.28599 63.9646 5.24296 63.8194 5.24296ZM65.314 5.64612C65.1504 5.67961 65.046 5.70194 64.9422 5.71637C64.8394 5.73394 64.7391 5.74987 64.5809 5.77329L64.5684 5.78541L64.5569 5.79508C64.5404 5.92062 64.5289 6.02914 64.5071 6.15672C64.4886 6.28866 64.4601 6.43857 64.4137 6.65397C64.3778 6.81886 64.3593 6.87632 64.3388 6.93433C64.3189 6.99233 64.2969 7.0487 64.2566 7.21087L64.266 7.22585L64.2739 7.2396C64.4218 7.23211 64.5185 7.2268 64.6179 7.22585C64.7171 7.22204 64.82 7.22585 64.9792 7.2268L64.9931 7.21468L65.008 7.20134C65.031 7.0551 65.0345 7.01575 65.0485 6.94441C65.0625 6.86788 65.0865 6.76195 65.1454 6.47901C65.1732 6.34612 65.2043 6.21363 65.2331 6.07802C65.2632 5.94295 65.2946 5.80992 65.3245 5.67702L65.32 5.66096L65.314 5.64612ZM65.3175 5.10254C65.1688 5.009 64.9078 5.03868 64.7321 5.16789C64.557 5.29452 64.537 5.47425 64.6852 5.56902C64.8314 5.65998 65.0934 5.63288 65.2676 5.50258C65.4423 5.37322 65.4642 5.19513 65.3175 5.10254ZM66.2169 7.26892C66.5177 7.26892 66.8261 7.18055 67.0583 6.9183C67.2369 6.70562 67.3188 6.38918 67.3471 6.25888C67.4395 5.82698 67.3676 5.62532 67.2772 5.50251C67.14 5.31529 66.8975 5.25524 66.646 5.25524C66.4947 5.25524 66.1344 5.27117 65.853 5.54771C65.6509 5.74719 65.5575 6.01787 65.5012 6.2774C65.4443 6.54182 65.3789 7.01784 65.7896 7.19498C65.9164 7.25299 66.0991 7.26892 66.2169 7.26892ZM66.1933 6.29714C66.2627 5.97008 66.3446 5.69558 66.5536 5.69558C66.7174 5.69558 66.7293 5.89982 66.6565 6.22797C66.6434 6.30082 66.5836 6.57164 66.5028 6.68697C66.4463 6.77207 66.3795 6.82367 66.3056 6.82367C66.2837 6.82367 66.153 6.82367 66.1509 6.61684C66.1499 6.51472 66.1695 6.41042 66.1933 6.29714ZM68.0992 7.22686L68.1227 7.20139C68.1561 7.01308 68.1616 6.96883 68.2354 6.58758C68.2723 6.40676 68.3487 6.00945 68.3846 5.86798C68.4526 5.83434 68.5184 5.80139 68.5843 5.80139C68.7355 5.80139 68.7171 5.94286 68.7111 5.99923C68.7056 6.094 68.6501 6.40295 68.5923 6.66833L68.5564 6.83962C68.5289 6.96624 68.499 7.08906 68.4725 7.2146L68.484 7.23965C68.793 7.22209 68.8833 7.22209 69.1477 7.22686L69.1797 7.20139C69.2261 6.90715 69.2386 6.82845 69.3255 6.40036L69.3668 6.20347C69.4496 5.81841 69.4915 5.6233 69.4302 5.46426C69.3624 5.28603 69.2032 5.243 69.06 5.243C68.965 5.243 68.7909 5.26737 68.6362 5.3488C68.5809 5.37971 68.5254 5.41524 68.471 5.45092L68.5184 5.25525L68.493 5.22217C68.1631 5.29352 68.0877 5.30305 67.7833 5.3488L67.7599 5.36691C67.723 5.67967 67.693 5.91468 67.5618 6.52958C67.5118 6.75615 67.4599 6.9849 67.4081 7.21092L67.422 7.23965C67.7349 7.22209 67.8283 7.22209 68.0992 7.22686ZM70.3689 7.23959C70.3883 7.13856 70.5037 6.53972 70.5047 6.53972C70.5047 6.53972 70.603 6.10033 70.609 6.0844C70.609 6.0844 70.6399 6.03865 70.6708 6.02054H70.7163C71.1454 6.02054 71.63 6.02054 72.0098 5.72276C72.2683 5.51852 72.445 5.21693 72.5238 4.85038C72.5442 4.76052 72.5593 4.65363 72.5593 4.54674C72.5593 4.40636 72.5329 4.26748 72.4565 4.15896C72.2628 3.87016 71.8771 3.86485 71.4319 3.86268C71.4305 3.86268 71.2124 3.86485 71.2124 3.86485C70.6424 3.87234 70.4139 3.87016 70.32 3.85791C70.3121 3.90216 70.2971 3.98086 70.2971 3.98086C70.2971 3.98086 70.093 4.98927 70.093 4.9909C70.093 4.9909 69.6044 7.13488 69.5814 7.23591C70.079 7.22951 70.2831 7.22951 70.3689 7.23959ZM70.7472 5.44826C70.7472 5.44826 70.9643 4.4419 70.9632 4.44571L70.9703 4.39411L70.9733 4.35476L71.0601 4.36429C71.0601 4.36429 71.5077 4.40527 71.5182 4.40636C71.6949 4.47921 71.7677 4.66697 71.7169 4.91206C71.6705 5.13605 71.5342 5.32436 71.359 5.41531C71.2148 5.49238 71.0381 5.49878 70.8561 5.49878H70.7383L70.7472 5.44826ZM72.0987 6.31578C72.0413 6.57639 71.9754 7.05241 72.3841 7.22206C72.5144 7.28116 72.6312 7.29872 72.7499 7.29232C72.8752 7.2851 72.9913 7.21811 73.0989 7.12171C73.0892 7.16133 73.0795 7.20096 73.0698 7.24072L73.0883 7.26618C73.3823 7.25297 73.4735 7.25297 73.792 7.25556L73.8208 7.23214C73.8674 6.94076 73.9112 6.65781 74.032 6.10037C74.0909 5.83336 74.1497 5.56894 74.2101 5.30302L74.2007 5.27375C73.8718 5.33869 73.7839 5.35258 73.4676 5.40037L73.4436 5.42121C73.4404 5.4483 73.4371 5.47431 73.434 5.50032C73.3848 5.41562 73.3135 5.34332 73.2035 5.29825C73.0629 5.2393 72.7325 5.31527 72.4485 5.59086C72.2489 5.78761 72.1531 6.05721 72.0987 6.31578ZM72.7893 6.33171C72.8597 6.01051 72.9406 5.73873 73.1501 5.73873C73.2826 5.73873 73.3524 5.86904 73.3382 6.09125C73.3269 6.14667 73.3148 6.20508 73.3004 6.27112C73.2794 6.36657 73.2567 6.4612 73.2346 6.55597C73.2121 6.62078 73.1859 6.68191 73.1571 6.72263C73.1032 6.80405 72.975 6.85457 72.9011 6.85457C72.8802 6.85457 72.7509 6.85457 72.7464 6.65142C72.7454 6.55025 72.7649 6.44609 72.7893 6.33171ZM76.396 5.27114L76.3706 5.24024C76.0452 5.3105 75.9863 5.32166 75.6873 5.36469L75.6653 5.38811C75.6643 5.39192 75.6634 5.39777 75.6619 5.40308L75.6609 5.39777C75.4383 5.945 75.4448 5.82695 75.2637 6.25776C75.2627 6.23816 75.2627 6.2259 75.2616 6.20507L75.2163 5.27114L75.1878 5.24024C74.8469 5.3105 74.8389 5.32166 74.5241 5.36469L74.4996 5.38811C74.4961 5.39927 74.4961 5.41153 74.4941 5.42487L74.4961 5.42964C74.5355 5.64395 74.526 5.59616 74.5655 5.93438C74.5839 6.10036 74.6084 6.26729 74.6268 6.43123C74.6579 6.70559 74.6752 6.84066 74.7132 7.25936C74.5006 7.63325 74.4502 7.77472 74.2456 8.10287L74.247 8.10614L74.1029 8.34905C74.0864 8.37465 74.0714 8.39221 74.0505 8.3997C74.0275 8.41182 73.9976 8.414 73.9561 8.414H73.8762L73.7575 8.83473L74.1647 8.84222C74.4037 8.84113 74.554 8.72199 74.6349 8.56187L74.8909 8.09429H74.8868L74.9137 8.06134C75.086 7.6662 76.396 5.27114 76.396 5.27114ZM72.0987 10.7969H71.9259L72.5652 8.54328H72.7773L72.8446 8.31113L72.8512 8.56929C72.8432 8.72887 72.961 8.87034 73.2705 8.84692H73.6283L73.7515 8.41298H73.6168C73.5394 8.41298 73.5035 8.39214 73.508 8.34748L73.5014 8.08483H72.8388V8.08619C72.6245 8.09096 71.9847 8.10811 71.8552 8.14488C71.6984 8.1879 71.5332 8.31453 71.5332 8.31453L71.5981 8.08211H70.9782L70.8491 8.54328L70.2012 10.8313H70.0755L69.9522 11.2621H71.1869L71.1455 11.4058H71.7538L71.7942 11.2621H71.9649L72.0987 10.7969ZM71.5921 9.00119C71.4928 9.03046 71.3081 9.11924 71.3081 9.11924L71.4724 8.54328H71.9649L71.8461 8.96293C71.8461 8.96293 71.6939 8.97246 71.5921 9.00119ZM71.6016 9.824C71.6016 9.824 71.4468 9.8447 71.345 9.86921C71.2447 9.90162 71.0567 10.0037 71.0567 10.0037L71.2263 9.40436H71.7214L71.6016 9.824ZM71.3256 10.8022H70.8316L70.9748 10.2963H71.4673L71.3256 10.8022ZM72.5154 9.40436H73.2275L73.1251 9.75756H72.4036L72.2952 10.1437H72.9266L72.4485 10.8611C72.4151 10.9138 72.385 10.9325 72.3517 10.9473C72.3182 10.9654 72.2743 10.9867 72.2234 10.9867H72.0483L71.9279 11.4096H72.3861C72.6242 11.4096 72.7649 11.2941 72.8687 11.1426L73.1966 10.6644L73.267 11.1499C73.2819 11.2409 73.3432 11.2941 73.3847 11.3148C73.4306 11.3393 73.478 11.3814 73.545 11.3877C73.6168 11.3909 73.6686 11.3935 73.7031 11.3935H73.9282L74.0634 10.9202H73.9746C73.9236 10.9202 73.8359 10.9111 73.8209 10.8941C73.806 10.8734 73.806 10.8415 73.7979 10.7931L73.7265 10.3064H73.4341L73.5623 10.1437H74.2825L74.3933 9.75756H73.7265L73.8304 9.40436H74.4951L74.6184 8.96878H72.6366L72.5154 9.40436ZM66.5008 10.9005L66.667 10.3112H67.3501L67.4749 9.87293H66.7912L66.8955 9.51019H67.5637L67.6875 9.08578H66.0157L65.8944 9.51019H66.2742L66.1729 9.87293H65.7921L65.6659 10.3187H66.0456L65.824 11.0984C65.7941 11.2016 65.8381 11.2409 65.8659 11.2889C65.8944 11.3356 65.9233 11.3665 65.9882 11.384C66.0551 11.4 66.101 11.4095 66.1633 11.4095H66.9335L67.0707 10.9239L66.7293 10.9739C66.6634 10.9739 66.4808 10.9655 66.5008 10.9005ZM66.5791 8.07945L66.406 8.41291C66.3689 8.48575 66.3356 8.53096 66.3056 8.55179C66.2791 8.56936 66.2268 8.57671 66.1509 8.57671H66.0605L65.9398 9.0033H66.2398C66.384 9.0033 66.4948 8.94693 66.5477 8.91874C66.6045 8.88634 66.6195 8.90485 66.6634 8.85965L66.7647 8.76611H67.7015L67.8258 8.32195H67.1401L67.2598 8.07945H66.5791ZM67.962 10.9091C67.946 10.8846 67.9575 10.8414 67.9819 10.7516L68.238 9.8484H69.1488C69.2815 9.84636 69.3773 9.84473 69.4397 9.83996C69.5066 9.83247 69.5794 9.80701 69.6588 9.76126C69.7407 9.71333 69.7826 9.66282 69.8179 9.60481C69.8574 9.54695 69.9208 9.42032 69.9752 9.22506L70.297 8.08213L69.3519 8.08799C69.3519 8.08799 69.0609 8.13374 68.9327 8.18425C68.8034 8.24062 68.6187 8.39802 68.6187 8.39802L68.704 8.08472H68.1202L67.3028 10.9739C67.2738 11.0861 67.2544 11.1675 67.2499 11.2164C67.2484 11.2691 67.3122 11.3212 67.3536 11.3606C67.4026 11.4 67.4749 11.3936 67.5442 11.4C67.6172 11.4058 67.7209 11.4095 67.8641 11.4095H68.3128L68.4506 10.9139L68.0489 10.9543C68.006 10.9543 67.9749 10.9298 67.962 10.9091ZM68.4032 9.23841H69.3598L69.299 9.44156C69.2904 9.44632 69.27 9.43148 69.1726 9.44374H68.3443L68.4032 9.23841ZM68.5948 8.55706H69.5595L69.4901 8.80174C69.4901 8.80174 69.0354 8.79697 68.9626 8.81127C68.6422 8.87036 68.455 9.05282 68.455 9.05282L68.5948 8.55706ZM69.3204 10.1219C69.3125 10.1522 69.3 10.1707 69.2825 10.1846C69.263 10.198 69.2316 10.2027 69.1847 10.2027H69.0484L69.0565 9.95533H68.4895L68.4665 11.1648C68.4656 11.2521 68.4735 11.3026 68.5334 11.3431C68.5933 11.3936 68.7779 11.4 69.0264 11.4H69.3817L69.51 10.9473L69.2007 10.9654L69.0979 10.9718C69.0838 10.9654 69.0704 10.9595 69.0554 10.9436C69.0424 10.9298 69.0204 10.9383 69.024 10.851L69.0264 10.541L69.3508 10.5267C69.526 10.5267 69.6008 10.4659 69.6647 10.4081C69.7257 10.3526 69.7456 10.2889 69.7686 10.2027L69.823 9.92823H69.3773L69.3204 10.1219Z" fill="#FEFEFE"/>
                                <rect x="84" y="0.432861" width="24" height="13.8253" fill="white"/>
                                <path d="M100.312 8.79913H101.683C101.722 8.79913 101.814 8.78631 101.853 8.78631C102.114 8.73503 102.336 8.50426 102.336 8.18374C102.336 7.87605 102.114 7.64528 101.853 7.58118C101.814 7.56836 101.735 7.56836 101.683 7.56836H100.312V8.79913Z" fill="url(#paint0_linear_20886_2315)"/>
                                <path d="M101.526 0.299072C100.221 0.299072 99.1498 1.33753 99.1498 2.63241V5.05548H102.506C102.584 5.05548 102.676 5.05548 102.741 5.0683C103.498 5.10676 104.06 5.49138 104.06 6.15805C104.06 6.68369 103.681 7.13241 102.976 7.22215V7.24779C103.746 7.29907 104.334 7.72215 104.334 8.376C104.334 9.08112 103.681 9.54266 102.819 9.54266H99.1367V14.2863H102.623C103.929 14.2863 105 13.2478 105 11.9529V0.299072H101.526Z" fill="url(#paint1_linear_20886_2315)"/>
                                <path d="M102.166 6.31166C102.166 6.00397 101.944 5.79884 101.683 5.76038C101.657 5.76038 101.592 5.74756 101.552 5.74756H100.312V6.87576H101.552C101.592 6.87576 101.67 6.87576 101.683 6.86294C101.944 6.82448 102.166 6.61935 102.166 6.31166Z" fill="url(#paint2_linear_20886_2315)"/>
                                <path d="M88.3897 0.299072C87.0838 0.299072 86.0131 1.33753 86.0131 2.63241V8.38882C86.679 8.70933 87.3711 8.91446 88.0632 8.91446C88.8859 8.91446 89.3299 8.42728 89.3299 7.76061V5.04266H91.367V7.74779C91.367 8.79907 90.701 9.65805 88.4419 9.65805C87.0708 9.65805 86 9.36318 86 9.36318V14.2734H89.4866C90.7924 14.2734 91.8632 13.235 91.8632 11.9401V0.299072H88.3897Z" fill="url(#paint3_linear_20886_2315)"/>
                                <path d="M94.9581 0.299072C93.6522 0.299072 92.5814 1.33753 92.5814 2.63241V5.68369C93.1821 5.18369 94.2268 4.86318 95.9113 4.9401C96.8123 4.97856 97.7787 5.22215 97.7787 5.22215V6.20933C97.2955 5.96574 96.7209 5.74779 95.9766 5.69651C94.6969 5.60676 93.9264 6.22215 93.9264 7.29907C93.9264 8.38882 94.6969 9.0042 95.9766 8.90164C96.7209 8.85036 97.2955 8.61959 97.7787 8.38882V9.376C97.7787 9.376 96.8254 9.61959 95.9113 9.65805C94.2268 9.73497 93.1821 9.41446 92.5814 8.91446V14.2991H96.068C97.3739 14.2991 98.4446 13.2606 98.4446 11.9657V0.299072H94.9581Z" fill="url(#paint4_linear_20886_2315)"/>
                                <defs>
                                <linearGradient id="paint0_linear_20886_2315" x1="99.1475" y1="8.18527" x2="105.016" y2="8.18527" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#007940"/>
                                <stop offset="0.2285" stop-color="#00873F"/>
                                <stop offset="0.7433" stop-color="#40A737"/>
                                <stop offset="1" stop-color="#5CB531"/>
                                </linearGradient>
                                <linearGradient id="paint1_linear_20886_2315" x1="99.1474" y1="7.28706" x2="105.016" y2="7.28706" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#007940"/>
                                <stop offset="0.2285" stop-color="#00873F"/>
                                <stop offset="0.7433" stop-color="#40A737"/>
                                <stop offset="1" stop-color="#5CB531"/>
                                </linearGradient>
                                <linearGradient id="paint2_linear_20886_2315" x1="99.1474" y1="6.31006" x2="105.016" y2="6.31006" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#007940"/>
                                <stop offset="0.2285" stop-color="#00873F"/>
                                <stop offset="0.7433" stop-color="#40A737"/>
                                <stop offset="1" stop-color="#5CB531"/>
                                </linearGradient>
                                <linearGradient id="paint3_linear_20886_2315" x1="86.0103" y1="7.28706" x2="91.9692" y2="7.28706" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#1F286F"/>
                                <stop offset="0.4751" stop-color="#004E94"/>
                                <stop offset="0.8261" stop-color="#0066B1"/>
                                <stop offset="1" stop-color="#006FBC"/>
                                </linearGradient>
                                <linearGradient id="paint4_linear_20886_2315" x1="92.5477" y1="7.28706" x2="98.3352" y2="7.28706" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#6C2C2F"/>
                                <stop offset="0.1735" stop-color="#882730"/>
                                <stop offset="0.5731" stop-color="#BE1833"/>
                                <stop offset="0.8585" stop-color="#DC0436"/>
                                <stop offset="1" stop-color="#E60039"/>
                                </linearGradient>
                                </defs>
                                </svg>

                            </span>
                            <span class="text-xs" style="font-size: 11px;font-weight: 400;color: #697386;" v-else >
                                {{ 
                                    paymentGateway.name === "ABA KHQR" ? 
                                        "Scan to pay with any banking app" 
                                    : paymentGateway.name === "Credit/Debit Card" ? 
                                        "Pay with <img style='width: 150px;h;height: 19px;' src='/storage/payment_getway/cards.png' class='h-4 inline-block' />"
                                    : "Pay with " + paymentGateway.name 
                                }}
                            </span>
                           
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-lg:hidden flex items-center justify-between gap-5 mt-10">
                <router-link :to="{ name: 'frontend.checkout.checkout' }"
                    class="field-button w-fit font-semibold tracking-wide normal-case text-secondary bg-[#F7F7FC]">
                    {{ $t('button.back_to_checkout') }}
                </router-link>

                <button @click.prevent="confirmOrder" class="field-button w-fit font-semibold tracking-wide normal-case">
                    {{ $t('button.confirm_order') }}
                </button>
            </div>
        </div>

        <div class="col-12 lg:col-4">
            <CouponComponent />
            <SummeryComponent />

            <div class="max-lg:flex hidden flex-col-reverse sm:flex-row items-center justify-between gap-5 mt-10">
                <router-link :to="{ name: 'frontend.checkout.checkout' }"
                    class="field-button font-semibold tracking-wide normal-case text-secondary bg-[#F7F7FC]">
                    {{ $t('button.back_to_checkout') }}
                </router-link>

                <button @click.prevent="confirmOrder($event)" class="field-button font-semibold tracking-wide normal-case">
                    {{ $t('button.confirm_order') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import statusEnum from "../../../../enums/modules/statusEnum";
import SummeryComponent from "../SummeryComponent.vue";
import CouponComponent from "../CouponComponent.vue";
import LoadingComponent from "../../components/LoadingComponent.vue";
import _ from "lodash";
import alertService from "../../../../services/alertService";
import sourceEnum from "../../../../enums/modules/sourceEnum";
import ENV from "../../../../config/env";
import ActivityEnum from "../../../../enums/modules/activityEnum";
import axios from "axios";

export default {
    name: "PaymentComponent",
    components: { CouponComponent, SummeryComponent, LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false
            },
            paymentGateways: [],
            credit: {},
            cashOnDelivery: {},
            statusEnum: statusEnum,
            sourceEnum: sourceEnum,
            ActivityEnum: ActivityEnum,
            form: {}
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        profile: function () {
            return this.$store.getters.authInfo;
        },
        paymentMethod: function () {
            return this.$store.getters['frontendCart/paymentMethod'];
        },
        subtotal: function () {
            return this.$store.getters['frontendCart/subtotal'];
        },
        discount: function () {
            return this.$store.getters['frontendCart/discount'];
        },
        total: function () {
            return this.$store.getters['frontendCart/total'];
        },
        orderType: function () {
            return this.$store.getters['frontendCart/orderType'];
        },
        getShippingAddress: function () {
            return this.$store.getters['frontendCart/shippingAddress'];
        },
        getBillingAddress: function () {
            return this.$store.getters['frontendCart/billingAddress'];
        },
        getOutletAddress: function () {
            return this.$store.getters['frontendCart/outletAddress'];
        },
        cartCoupon: function () {
            return this.$store.getters['frontendCart/coupon'];
        },
        products: function () {
            return this.$store.getters['frontendCart/lists'];
        },
        shippingCharge: function () {
            return this.$store.getters['frontendCart/shippingCharge']
        },
        totalTax: function () {
            return this.$store.getters['frontendCart/totalTax'];
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch('frontendPaymentGateway/lists', { status: this.statusEnum.ACTIVE }).then(res => {
            if (res.data.data.length > 0) {
                _.forEach(res.data.data, (gateway) => {
                    if (gateway.slug === "credit") {
                        this.credit = gateway;
                    } else if (gateway.slug === "cashondelivery") {
                        this.cashOnDelivery = gateway;
                        if(this.setting.site_cash_on_delivery === this.ActivityEnum.ENABLE){
                            this.selectPaymentMethod(this.cashOnDelivery);
                        }

                    } else {
                        this.paymentGateways.push(gateway);
                        console.log(this.paymentGateways);
                        
                    }
                });
            }
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        selectPaymentMethod: function (paymentMethod) {
            this.$store.dispatch("frontendCart/paymentMethod", paymentMethod);
        },
        confirmOrder: function (e) {
            this.form = {
                subtotal: this.subtotal,
                discount: this.discount,
                tax: this.totalTax,
                shipping_charge: this.shippingCharge,
                total: this.total,
                order_type: this.orderType,
                shipping_id: Object.keys(this.getShippingAddress).length > 0 ? this.getShippingAddress.id : 0,
                billing_id: Object.keys(this.getBillingAddress).length > 0 ? this.getBillingAddress.id : 0,
                outlet_id: Object.keys(this.getOutletAddress).length > 0 ? this.getOutletAddress.id : 0,
                coupon_id: Object.keys(this.cartCoupon).length > 0 ? this.cartCoupon.id : 0,
                source: sourceEnum.WEB,
                payment_method: Object.keys(this.paymentMethod).length > 0 ? this.paymentMethod.id : 0,
                products: JSON.stringify(this.products)
            };

            this.$store.dispatch('frontendOrder/save', this.form).then(orderResponse => {
                this.loading.isActive = false;
                const paymentSlug = Object.keys(this.paymentMethod).length > 0 ? this.paymentMethod.slug : '';
                const orderId = orderResponse.data.data.id;

                if (paymentSlug) {
                    if (paymentSlug === 'card' || paymentSlug === 'aba') {
                        axios.get("/get_aba_config_data", {
                            params: {
                                methud: paymentSlug,
                                order_id: orderId
                            }
                        }).then(response => {
                            if (!response.data.success) {
                                alert("Something went wrong: " + response.data.message);
                                return;
                            }

                            const formData = response.data.form_data;
                            const existingForm = document.getElementById('aba_merchant_request');
                            if (existingForm) existingForm.remove();
                            // Create new hidden form
                            const form = document.createElement("form");
                            form.method = "POST";
                            form.target = "aba_webservice";
                            form.action = "https://checkout.payway.com.kh/api/payment-gateway/v1/payments/purchase";
                            form.id = "aba_merchant_request";
                            form.style.display = "none";

                            for (const key in formData) {
                                const input = document.createElement("input");
                                input.type = "hidden";
                                input.name = key;
                                input.value = formData[key];
                                form.appendChild(input);
                            }

                            document.body.appendChild(form);
                            function waitForAbaAndCheckout() {
                                if (typeof AbaPayway !== 'undefined' && typeof AbaPayway.checkout === 'function') {
                                    AbaPayway.checkout();
                                } else {
                                    setTimeout(waitForAbaAndCheckout, 200);
                                }
                            }

                            if (!document.getElementById("aba_script_loader")) {
                                const script = document.createElement("script");
                                script.id = "aba_script_loader";
                                script.src = "https://checkout.payway.com.kh/plugins/checkout2-0.js";
                                script.onload = waitForAbaAndCheckout;
                                document.body.appendChild(script);
                            } else {
                                waitForAbaAndCheckout();
                            }

                        }).catch(error => {
                            console.error("ABA Checkout Error:", error);
                            alert("Unable to connect to payment server.");
                        });

                    } else {
                        // Other payment methods
                        window.location.href = ENV.API_URL + "/payment/" + paymentSlug + "/pay/" + orderId;
                    }
                } else {
                    alertService.error(this.$t('message.payment_method_required'));
                }
            }).catch((err) => {
                this.loading.isActive = false;
                if (typeof err.response.data.errors === 'object') {
                    _.forEach(err.response.data.errors, (error) => {
                        alertService.error(error[0]);
                    });
                }
            });
        }
    }
}
</script>