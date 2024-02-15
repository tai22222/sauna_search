<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
// import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import CreateSaunaInfo from '@/Pages/Sauna/CreateSaunaInfo.vue';

import { Link, router, useForm, usePage } from '@inertiajs/vue3';

const { saunas, saunaInfos } = usePage().props;


defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});

</script>

<template>
    <AppLayout title="Sauna施設情報">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                サウナ施設
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                    <!-- 子コンポーネント(CreateSaunaInfo)へオブジェクト、配列の受け渡し -->
                    <CreateSaunaInfo 
                      :user="$page.props.auth.user"
                      :sauna="saunas"
                      :facilityType="$page.props.facilityTypes"
                      :usageType="$page.props.usageTypes"
                      :prefecture="$page.props.prefectures"
                      :saunaInfo="saunaInfos"
                      :saunaType="$page.props.saunaTypes"
                      :stoveType="$page.props.stoveTypes"
                      :heatType="$page.props.heatTypes"
                      :waterBath="$page.props.waterBaths"
                      :bathType="$page.props.bathsTypes"
                      :waterType="$page.props.waterTypes"
                    />

                    <SectionBorder />
                </div>

                <!-- todo 管理者権限の時にサウナ情報削除ページを表示 -->

            </div>
        </div>
    </AppLayout>
</template>
