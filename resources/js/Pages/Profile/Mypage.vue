<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ShowProfile from '@/Pages/Profile/ShowProfile.vue';

import SectionBorder from '@/Components/SectionBorder.vue';

// defineProps({
//     confirmsTwoFactorAuthentication: Boolean,
//     sessions: Array,
//     users: Object,
// });

const props = defineProps({
  confirmsTwoFactorAuthentication: Boolean,
  sessions: Array,
  user: Object,
  reviews: Object,
  favorites: Object,
  reviewsCount: Number,
  favoritesCount: Number,
});
</script>

<template>
    <AppLayout title="マイページ">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                マイページ
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div v-if="$page.props.jetstream.canUpdateProfileInformation">

                    <ShowProfile 
                      :user="$page.props.auth.user"
                      :reviews="$page.props.reviews"
                      :favorites="$page.props.favorites"
                       />

                    <SectionBorder />
                </div>

                <!-- <div v-if="$page.props.jetstream.canUpdatePassword">
                    <UpdatePasswordForm class="mt-10 sm:mt-0" />

                    <SectionBorder />
                </div> -->


            </div>
        </div>
    </AppLayout>
</template>
