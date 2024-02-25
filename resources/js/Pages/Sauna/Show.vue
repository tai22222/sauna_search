<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, defineProps } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia'

import SectionBorder from '@/Components/SectionBorder.vue';

// ベースURLの設定
const baseUrl = import.meta.env.VITE_APP_BASE_URL; 

// 親コンポーネント(Create.vue)からオブジェクト、配列の受け渡し(CompositionAPI、ObjectはArrayも含む)
const props = defineProps({
    sauna: Object,
    auth: Object,
});

const { sauna, auth } = usePage().props;

// propsから取得したデータがからの場合は指定の文字列を表示する
// 住所2
const saunaAddress2 = computed(() => {
  return sauna.address2 ? sauna.address2 : '';
});

// 住所3
const saunaAddress3 = computed(() => {
  return sauna.address3 ? sauna.address3 : '';
});

// 料金詳細の\r\n部分をbrタグに変換
const formattedFeeText = computed(() => {
  return sauna.fee_text ? sauna.fee_text.replace(/\r\n/g, '<br>') : '';
});

// 定休日の有無
const businessHourIsClosed = computed(() => {
  return sauna.business_hours && sauna.business_hours.is_closed ? 'あり' : 'なし';
});

// サウナの温度
const saunaTemperature = computed(() => {
  return sauna.sauna_info && sauna.sauna_info.temperature ? sauna.sauna_info.temperature : '-';
});

// サウナの収容人数
const saunaCapacity = computed(() => {
  return sauna.sauna_info && sauna.sauna_info.capacity ? sauna.sauna_info.capacity : '-';
});

// サウナの追加情報
const saunaAdditionalInfo = computed(() => {
  return sauna.sauna_info && sauna.sauna_info.additional_info ? sauna.sauna_info.additional_info : '-';
});

// サウナのタイプ
const saunaType = computed(() => {
  return sauna.sauna_info.sauna_type && sauna.sauna_info.sauna_type.type_name ? sauna.sauna_info.sauna_type.type_name : '';
});

// ストーブのタイプ
const stoveType = computed(() => {
  return sauna.sauna_info.stove_type && sauna.sauna_info.stove_type.type_name ? sauna.sauna_info.stove_type.type_name : '';
});

// 熱源のタイプ
const heatType = computed(() => {
  return sauna.sauna_info.heat_type && sauna.sauna_info.heat_type.source_name ? sauna.sauna_info.heat_type.source_name : '';
});

// 水風呂の温度
const waterBathTemperature = computed(() => {
  return sauna.water_bath && sauna.water_bath.temperature ? sauna.water_bath.temperature : '-';
});

// 水風呂の収容人数
const waterBathCapacity = computed(() => {
  return sauna.water_bath && sauna.water_bath.capacity ? sauna.water_bath.capacity : '-';
});

// 水風呂の水深
const waterDepth = computed(() => {
  return sauna.water_bath && sauna.water_bath.water_deep ? sauna.water_bath.water_deep : '';
});

// 水風呂の水深
const waterBathAdditionalInfo = computed(() => {
  return sauna.water_bath && sauna.water_bath.additional_info ? sauna.water_bath.additional_info : '';
});

// 水の種類
const waterType = computed(() => {
  return sauna.water_bath.water_type && sauna.water_bath.water_type.type_name ? sauna.water_bath.water_type.type_name : '';
});

// 水風呂の種類
const bathType = computed(() => {
  return sauna.water_bath.bath_type && sauna.water_bath.bath_type.type_name ? sauna.water_bath.bath_type.type_name : '';
});

//営業時間のフォーマット変換(DBから渡ってくるのはH:i:sなのでH:iに変更、24時を回っている時は翌○○時の記述に変換)
const formattedBusinessHours = computed(() => {
  if(sauna.business_hours && (sauna.business_hours.opening_time || sauna.business_hours.closing_time) ){
    return props.sauna.business_hours.map((hour) => {
    let openingTime = hour.opening_time.substr(0, 5);
    let closingTime = hour.closing_time.substr(0, 5);
    let nextDay = '';

    // 終了時間が開始時間より前の場合、"翌"を追加
    if (hour.closing_time < hour.opening_time) {
      nextDay = '翌';
    }

    return {
      ...hour,
      opening_time: openingTime,
      closing_time: `${nextDay}${closingTime}`
    };
  });
  }
});

// 施設画像
const images = [
  {'image_url': sauna.images_facility.main_image_url, 'alt': 'メイン画像'},
  {'image_url': sauna.images_facility.image1_url, 'alt': 'サウナ施設関連画像'},
  {'image_url': sauna.images_facility.image2_url, 'alt': 'サウナ施設関連画像'},
  {'image_url': sauna.images_facility.image3_url, 'alt': 'サウナ施設関連画像'},
  {'image_url': sauna.images_facility.image4_url, 'alt': 'サウナ施設関連画像'},
  {'image_url': sauna.images_facility.image5_url, 'alt': 'サウナ施設関連画像'},
];

// 編集ページへ遷移
const goToEditPage = () => {
  // ログインしているかどうかを判定
  if (!auth.user) {
    // ログインしていない場合はログインページにリダイレクト
    Inertia.visit('/login');
  } else {
    // ログインしている場合は編集ページ（例）に遷移
    Inertia.visit(`${baseUrl}saunas/edit/${sauna.id}`);
  }
};

const goBack = () => {
  window.history.back();
}

console.log(props);
</script>

<template>
    <AppLayout title="Sauna">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                施設詳細ページ
            </h2>
        </template>

        <div>
          <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
              <div v-if="$page.props.jetstream.canUpdateProfileInformation">

                    <div class="flex justify-end mr-6 mb-6">
                      <!-- <span>表示: {{ saunas.from }} - {{ saunas.to }} / {{ saunas.total }} 件</span> -->
                      <div class="">
                        <a @click.prevent="goToEditPage()"
                                class="flex items-center justify-center px-2 py-1 hover:bg-gray-300 border-b-2"
                                href="#">施設情報の編集</a>
                      </div>
                    </div>

                    <div class="mb-16 px-4 py-5 bg-white sm:p-20 shadow rounded-2xl">
                      <div class="grid grid-cols-6 gap-4">
                        <div class="col-span-6 text-3xl font-bold">
                          {{ sauna.facility_name }}
                        </div>
                        <div class="col-span-1">
                          {{ sauna.facility_type.type_name}}
                        </div>
                        <div class="col-span-2">
                          {{ sauna.prefecture.name }} - {{ sauna.address1 }}
                        </div>
                        <!-- <div class="col-span-6">
                          <button>Twitterツイート</button>
                          <button>フェイスブックシェア</button>
                        </div> -->
                        <div class="col-span-6">
                          <ul class="grid grid-cols-4 text-xl">
                            <li><a href="" class="border-b-4 border-blue-200">施設情報</a></li>
                            <li><a href="">サ活</a></li>
                            <li><a href="">サウナ飯</a></li>
                          </ul>
                        </div>
                      </div>

                      <SectionBorder />

                      <div class="grid grid-cols-2 gap-16 mb-12">
                        <div class="md:col-span-1 col-span-2 px-6 py-6 border-2 rounded-xl text-center">
                          <div class="mb-2 text-xl font-bold text-red-500">サウナ室</div>
                          <div class="mb-2">温度<span class="text-3xl text-red-500">{{ saunaTemperature }}</span><span class="text-red-500">度</span></div>
                          <div class="mb-2">収容人数： {{ saunaCapacity }} 人</div>
                          <div class="mb-4">
                            <ul class="flex justify-center text-white">
                              <li v-if="saunaType" class="bg-gray-400 px-2 py-1 font-bold border-2 rounded-2xl">{{ saunaType }}</li>
                              <li v-if="stoveType" class="bg-gray-400 px-2 py-1 font-bold border-2 rounded-2xl">{{ stoveType }}</li>
                              <li v-if="heatType" class="bg-gray-400 px-2 py-1 font-bold border-2 rounded-2xl">{{ heatType }}</li>
                            </ul>
                          </div>
                          <div>{{ saunaAdditionalInfo }}</div>
                        </div>
                        <div class="md:col-span-1 col-span-2 px-6 py-6 border-2 rounded-xl text-center">
                          <div class="mb-2 text-xl font-bold text-blue-500">水風呂情報</div>
                          <div class="mb-2">温度<span class="text-3xl text-blue-500">{{ waterBathTemperature }}</span><span class="text-blue-500">度</span></div>
                          <div class="mb-2">収容人数： {{ waterBathCapacity }} 人</div>
                          <div class="mb-4">
                            <ul class="flex justify-center text-white">
                              <li v-if="waterType" class="bg-gray-400 px-2 py-1 font-bold border-2 rounded-2xl">{{ waterType }}</li>
                              <li v-if="bathType" class="bg-gray-400 px-2 py-1 font-bold border-2 rounded-2xl">{{ bathType }}</li>
                              <li v-if="waterDepth" class="bg-gray-400 px-2 py-1 font-bold border-2 rounded-2xl">{{ waterDepth }}</li>
                            </ul>
                          </div>
                          <div>{{ waterBathAdditionalInfo }}</div>
                        </div>
                      </div>

                      <div class="grid grid-cols-2 gap-6 mb-12">
                        <div class="col-span-2">基本情報</div>
                        <div class="col-span-1 mr-6">
                          <div class="w-96">
                            <span class="block rounded w-full bg-cover bg-no-repeat bg-center" 
                                  :style="{ backgroundImage: `url(http://localhost/storage/${sauna.images_facility.main_image_url})`, paddingBottom: '100%' }"></span>
                            <img :src="`http://localhost/storage/default-images/no_image.jpg`" 
                                        :alt="sauna.facility_name" 
                                        class="rounded w-full object-cover">
                          </div>
                          <div>
                            マップ
                          </div>
                        </div>
                        <div class="col-span-1">
                          <div class="grid grid-cols-9 gap-6">
                            <div class="col-span-2 font-bold">
                              施設名
                            </div>
                            <div class="col-span-7">
                              {{ sauna.facility_name }}
                            </div>
                            <div class="col-span-2 font-bold">
                              施設タイプ
                            </div>
                            <div class="col-span-7">
                              {{ sauna.facility_type.type_name }}
                            </div>
                            <div class="col-span-2 font-bold">
                              住所
                            </div>
                            <div class="col-span-7">
                              {{ sauna.prefecture.name }} {{ sauna.address1 }} {{ saunaAddress2 }} {{ saunaAddress3 }}
                            </div>
                            <div class="col-span-2 font-bold">
                              アクセス
                            </div>
                            <div class="col-span-7">
                              {{ sauna.access_text }}
                            </div>
                            <div class="col-span-2 font-bold">
                              TEL
                            </div>
                            <div class="col-span-7">
                              {{ sauna.tel }}
                            </div>
                            <div class="col-span-2 font-bold">
                              HP
                            </div>
                            <div class="col-span-7">
                              <a :href="sauna.website_url">{{ sauna.website_url }}</a>
                            </div>
                            <div class="col-span-2 font-bold">
                              定休日
                            </div>
                            <div class="col-span-7">
                              {{ businessHourIsClosed }}
                            </div>
                            <div class="col-span-2 font-bold">
                              営業時間
                            </div>
                            <div class="col-span-7">
                              <ul v-for="hour in formattedBusinessHours" :key="hour.id">
                                <li> {{ hour.day_of_week }}曜日 ： {{ hour.opening_time }} ~ {{ hour.closing_time }}</li>
                              </ul>
                            </div>
                            <div class="col-span-2 font-bold">
                              料金
                            </div>
                            <div class="col-span-7">
                              <div v-html="formattedFeeText"></div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <SectionBorder />

                      <div class="grid grid-cols-6 gap-4 mb-12">
                        <div class="col-span-6 mb-6">
                          写真ギャラリー
                        </div>
                        <div v-for="image in images" class="sm:col-span-3 md:col-span-2 col-span-6">
                          <div v-if="image.image_url">
                            <p>{{ image.alt }}</p>
                              <span class="block rounded w-full bg-cover bg-no-repeat bg-center"
                                    :style="{ backgroundImage: `url(http://localhost/storage/${image.image_url})`, 
                                    paddingBottom: '100%' }"></span>
                            </div>
                          </div>
                      </div>
                      <div>
                        <button @click="goBack">戻る</button>
                        <!-- ページネーション -->
                        <!-- <nav>
                          <ul class="flex justify-center space-x-6"> -->

                            <!-- 1ページ前へ遷移 -->
                            <!-- <li v-if="saunas.current_page > 1">
                              <a @click.prevent="goToPreviousPage()"
                                class="flex items-center justify-center w-12 h-12 bg-gray-200 hover:bg-gray-300 rounded-full"
                                href="#">&lt;</a>
                            </li> -->
                            <!-- 1ページ次へ遷移 -->
                            <!-- <li v-if="saunas.current_page < saunas.last_page">
                              <a @click.prevent="goToNextPage()"
                                class="flex items-center justify-center w-12 h-12 bg-gray-200 hover:bg-gray-300 rounded-full"
                                href="#">&gt;</a>
                            </li>
                          </ul>
                        </nav> -->
                      </div>
                    </div>


                </div>
            </div>
        </div>
    </AppLayout>
</template>
