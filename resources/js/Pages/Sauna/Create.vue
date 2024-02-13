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
                Profile
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

                <!-- <div v-if="$page.props.jetstream.canUpdatePassword">
                    <UpdatePasswordForm class="mt-10 sm:mt-0" />

                    <SectionBorder />
                </div> -->

                <!-- <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                    <TwoFactorAuthenticationForm
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                        class="mt-10 sm:mt-0"
                    />

                    <SectionBorder />
                </div> -->

                <!-- <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" /> -->

                <!-- <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                    <SectionBorder />

                    <DeleteUserForm class="mt-10 sm:mt-0" />
                </template> -->
            </div>
        </div>
    </AppLayout>
</template>
