<script setup>

import { ref, onMounted } from 'vue';

// vendorからコンポーネントの読み込み
import { Head, Link, router, usePage } from '@inertiajs/vue3';

// コンポーネントの読み込み
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

// propsの定義
const props = defineProps({
    title: String,
});

// ページ上に関する情報の取得
const { flash } = usePage().props;

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

// フラッシュメッセージに関する定義
const successMessage = ref('');
const errorMessage = ref('');

// フラッシュメッセージをセットアップする関数
const setupFlashMessages = () => {
  // 成功メッセージの取得
  const successMsg = flash.successMessage;
  if (successMsg) {
    successMessage.value = successMsg;
    setTimeout(() => {
      successMessage.value = '';
    }, 3000);
  }
  
  // エラーメッセージの取得
  const errorMsg = flash.errorMessage;
  if (errorMsg) {
    errorMessage.value = errorMsg;
    setTimeout(() => {
      errorMessage.value = '';
    }, 3000);
  }
};
// console.log(flash);

// コンポーネントがマウントされた時にフラッシュメッセージをセットアップする
onMounted(() => {
  if (flash && (flash.success || flash.error)) {
    setupFlashMessages();
  }
});

// ログアウトに関する定義
const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen">
          <nav class="bg-main border-b border-gray-100">

                <!-- ヘッダー左側 メニュー -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- ロゴ -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('sauna.index')">
                                    <ApplicationMark class="block h-9 w-auto" />
                                </Link>
                            </div>

                            <!-- ナビゲーションリンク -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('sauna.index')" :active="route().current('sauna.index')">
                                    TOP
                                </NavLink>
                            </div>
                            <div v-if="$page.props.auth.user" class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('sauna.create')" :active="route().current('sauna.create')">
                                    サウナ施設の追加
                                </NavLink>
                            </div>
                            
                            <div v-if="!$page.props.auth.user" class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('login')">
                                    ログイン
                                </NavLink>
                            </div>
                            <div v-if="!$page.props.auth.user" class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('register')">
                                    新規登録
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <div class="ml-3 relative">
                                <!-- Teams Dropdown チーム機能がtrueの時に表示-->
                                <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.current_team.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Manage Team
                                                </div>

                                                <!-- Team Settings -->
                                                <DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">
                                                    Team Settings
                                                </DropdownLink>

                                                <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">
                                                    Create New Team
                                                </DropdownLink>

                                                <div class="border-t border-gray-200" />

                                                <!-- Team Switcher -->
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Switch Teams
                                                </div>

                                                <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                                    <form @submit.prevent="switchToTeam(team)">
                                                        <DropdownLink as="button">
                                                            <div class="flex items-center">
                                                                <svg v-if="team.id == $page.props.auth.user.current_team_id" class="mr-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>

                                                                <div>{{ team.name }}</div>
                                                            </div>
                                                        </DropdownLink>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Settings Dropdown プロフィール機能がtrueの時に表示-->
                            <div v-if="$page.props.auth.user" class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" 
                                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" 
                                                 :src="$page.props.auth.user.profile_photo_url" 
                                                 :alt="$page.props.auth.user.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" 
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            アカウント設定
                                        </div>

                                        <DropdownLink :href="route('profile.mypage')">
                                            マイページ
                                        </DropdownLink>
                                        <DropdownLink :href="route('profile.show')">
                                            プロフィール
                                        </DropdownLink>
                                        <DropdownLink :href="route('profile.show')">
                                            パスワード変更
                                        </DropdownLink>

                                        <div class="border-t border-gray-200" />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                ログアウト
                                            </DropdownLink>
                                        </form>
                                        <DropdownLink :href="route('profile.show')" class="bg-red-200" classOverride="hover:bg-red-400">
                                            アカウント削除
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>


                        <!-- Hamburgermenu -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" @click="showingNavigationDropdown = ! showingNavigationDropdown">
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ハンバーガーメニュー 開封時 -->
                <div v-if="$page.props.auth.user" 
                    :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" 
                    class="sm:hidden">

                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('sauna.index')" :active="route().current('sauna.index')">
                            TOP
                        </ResponsiveNavLink>
                    </div>
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('sauna.create')" :active="route().current('sauna.create')">
                            サウナ施設の追加
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos && $page.props.auth.user" class="shrink-0 mr-3">
                                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </div>

                            <div v-if="$page.props.auth.user">
                                <div class="font-medium text-base text-gray-800">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.mypage')" :active="route().current('profile.mypage')">
                                マイページ
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                プロフィール
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                パスワード変更
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <div v-if="$page.props.auth.user">
                              <form method="POST" @submit.prevent="logout">
                                  <ResponsiveNavLink as="button">
                                      ログアウト
                                  </ResponsiveNavLink>
                              </form>
                              <!-- プロフィール編集ページへ遷移 -->
                              <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')"
                              class="bg-red-300" >
                                  アカウント削除
                              </ResponsiveNavLink>
                            </div>
                            <!-- Team Management -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                <div class="border-t border-gray-200" />

                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Manage Team
                                </div>

                                <!-- Team Settings -->
                                <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)" :active="route().current('teams.show')">
                                    Team Settings
                                </ResponsiveNavLink>

                                <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')" :active="route().current('teams.create')">
                                    Create New Team
                                </ResponsiveNavLink>

                                <div class="border-t border-gray-200" />

                                <!-- Team Switcher -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Switch Teams
                                </div>

                                <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                    <form @submit.prevent="switchToTeam(team)">
                                        <ResponsiveNavLink as="button">
                                            <div class="flex items-center">
                                                <svg v-if="team.id == $page.props.auth.user.current_team_id" class="mr-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <div>{{ team.name }}</div>
                                            </div>
                                        </ResponsiveNavLink>
                                    </form>
                                </template>
                            </template>

                        </div>
                    </div>
                </div>

                <!-- ハンバーガーメニュー 開封時(未ログイン時) -->
                <div v-else :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('sauna.index')" :active="route().current('sauna.index')">
                            TOP
                        </ResponsiveNavLink>
                    </div>
                    <!-- Responsive Settings Options -->
                    <div class="pt-2 pb-1 border-t border-gray-200">
                        <div class="mt-1 space-y-1">
                            <ResponsiveNavLink :href="route('login')">
                                ログイン
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('register')">
                                新規登録
                            </ResponsiveNavLink>

                        </div>
                    </div>
                </div>
            </nav>

            <!-- ヘッダー -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- フラッシュメッセージの表示 -->
            <transition name="flash">
              <div v-if="successMessage" class="text-center bg-green-500/70 text-white font-bold p-4 rounded mb-4" role="alert">
              {{ successMessage }}
            </div>
            </transition>
            <transition name="flash">
              <div v-if="errorMessage" class="text-center bg-red-500/70 text-white font-bold p-4 rounded mb-4" role="alert">
              {{ errorMessage }}
            </div>
          </transition>

            <!-- ページメイン コンポーネント -->
            <main class="bg-gray-200 text-gray-700">
                <slot />
            </main>
        </div>
    </div>
</template>