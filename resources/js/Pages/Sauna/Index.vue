<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {
  ref,
  computed,
  defineProps,
  onMounted,
  onErrorCaptured,
  onUnmounted,
} from "vue";

import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";

import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Textarea from "@/Components/Textarea.vue";
import SelectBox from "@/Components/SelectBox.vue";
import SectionBorder from "@/Components/SectionBorder.vue";

// .envからbaseURLの設定
const baseUrl = import.meta.env.VITE_APP_BASE_URL;

// 親コンポーネント(Create.vue)からオブジェクト、配列の受け渡し(CompositionAPI、ObjectはArrayも含む)
const props = defineProps({
  saunas: Object,
  prefectures: Array,
  filters: Object,
});

// ウインドウの表示サイズを取得
const windowWidth = ref(window.innerWidth);

// ウインドサイズが変更されたら呼び出される
const handleResize = () => {
  windowWidth.value = window.innerWidth;
};

onMounted(() => {
  window.addEventListener("resize", handleResize);
});

onUnmounted(() => {
  window.removeEventListener("resize", handleResize);
});

// 画面サイズが810px未満の時は次へボタンと前へボタンは非表示
const isLargeScreen = computed(() => {
  return windowWidth.value > 810;
});

onErrorCaptured((err, instance, info) => {
  console.error("Error caught during component initialization:", err);
  // ここでエラー情報を詳細にログ出力
  return false; // エラーを伝播させない
});

const { prefectures } = usePage().props;

const initialFilters = props.filters || {};

// 検索フォームの受け渡し値
const formData = ref({
  prefecture: initialFilters.prefecture || "",
  // condition: '',
  // keyword: '',
  sauna_temperature_from: initialFilters.sauna_temperature_from || "",
  sauna_temperature_to: initialFilters.sauna_temperature_to || "",
  water_bath_temperature_from: initialFilters.water_bath_temperature_from || "",
  water_bath_temperature_to: initialFilters.water_bath_temperature_to || "",
});

// 検索の一覧
// 都道府県
const prefectureArray = Array.isArray(prefectures)
  ? prefectures
  : [prefectures];
// const selectedPrefecture = ref(null);

// サウナ温度(40~150)
const saunaTemp = [
  { id: 40, temp: 40 },
  { id: 50, temp: 50 },
  { id: 60, temp: 60 },
  { id: 70, temp: 70 },
  { id: 80, temp: 80 },
  { id: 90, temp: 90 },
  { id: 100, temp: 100 },
  { id: 110, temp: 110 },
  { id: 120, temp: 120 },
  { id: 130, temp: 130 },
  { id: 140, temp: 140 },
  { id: 150, temp: 150 },
];
// 水風呂温度(0~30)
const waterTemp = [
  { id: 0, temp: 0 },
  { id: 5, temp: 5 },
  { id: 10, temp: 10 },
  { id: 11, temp: 11 },
  { id: 12, temp: 12 },
  { id: 13, temp: 13 },
  { id: 14, temp: 14 },
  { id: 15, temp: 15 },
  { id: 16, temp: 16 },
  { id: 17, temp: 17 },
  { id: 18, temp: 18 },
  { id: 19, temp: 19 },
  { id: 20, temp: 20 },
  { id: 25, temp: 25 },
  { id: 30, temp: 30 },
];

// 検索、ソート、ページネーションの状態をURLのクエリパラメータに合わせて更新
const updateDataAndView = (additionalParams = {}) => {
  // 無効な選択肢をフィルタリング
  const validParams = Object.fromEntries(
    Object.entries({ ...formData.value, ...additionalParams }).filter(
      ([key, value]) =>
        value !== "選択してください" && value !== null && value !== ""
    )
  );

  // クエリパラメータを生成
  const searchParams = new URLSearchParams(validParams).toString();

  // サーバーにリクエストを送信
  Inertia.get(`${baseUrl}saunas?${searchParams}`);
};

// 検索ボタン押下時の処理
const submitSearch = () => updateDataAndView();

// ソート機能
const sortSaunas = (condition, order) =>
  updateDataAndView({ sort: condition, order: order });

// 指定ページへの遷移
const paginateTo = (page) => updateDataAndView({ page });

// ページネーション
// 現在のページ
const currentPage = ref(props.saunas.current_page);

// 表示ページが5以上の時と、5未満の時の表示するページ数を配列に格納
const pagesToShow = computed(() => {
  let pages = [];
  let startPage = Math.max(currentPage.value - 2, 1);
  let endPage = Math.min(startPage + 4, props.saunas.last_page);

  // 最初のページが1より大きい場合、開始ページを調整
  if (endPage - startPage < 4) {
    startPage = Math.max(endPage - 4, 1);
  }

  for (let page = startPage; page <= endPage; page++) {
    pages.push(page);
  }
  return pages;
});

// 表示件数
const itemsPerPage = ref(5);
// 総件数
const totalSaunaItems = ref(props.saunas.total);
// 最終ページの計算
const totalPages = computed(() =>
  Math.ceil(totalSaunaItems.value / itemsPerPage.value)
);

// 前へボタンのクリック時の処理
const goToPreviousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    const params = new URLSearchParams(formData.value).toString();
    Inertia.visit(`saunas?page=${props.saunas.current_page - 1}&${params}`, {
      preserveState: true,
    });
  }
};

// 次へボタンのクリック時の処理
const goToNextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
    const params = new URLSearchParams(formData.value).toString();
    Inertia.visit(`saunas?page=${props.saunas.current_page + 1}&${params}`, {
      preserveState: true,
    });
  }
};

// 最初のページに移動
const goToFirstPage = () => {
  const params = new URLSearchParams(formData.value).toString();
  Inertia.visit(`saunas?page=1&${params}`, { preserveState: true });
};

// 最後のページに移動
const goToLastPage = () => {
  const params = new URLSearchParams(formData.value).toString();
  Inertia.visit(`saunas?page=${props.saunas.last_page}&${params}`, {
    preserveState: true,
  });
};

// 時間の表示フォーマットの変更
const formatTime = (time) => {
  if (!time) return "";
  const [hour, minute] = time.split(":");
  return `${hour.padStart(2, "0")}:${minute.padStart(2, "0")}`;
};
</script>

<template>
  <AppLayout title="Sauna">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        サウナ一覧
      </h2>
    </template>

    <div>
      <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div v-if="$page.props.jetstream.canUpdateProfileInformation">
          <!-- 検索フォーム -->
          <FormSection @submitted="submitSearch" class="mb-16 rounded-full">
            <template #title> </template>
            <template #description> </template>

            <template #form>
              <!-- 地域(都道府県) select -->
              <div class="col-span-6 sm:col-span-3 md:col-span-2">
                <SelectBox
                  id="prefecture"
                  label="都道府県"
                  column="name"
                  v-model="formData.prefecture"
                  :initialValue="initialFilters.prefecture"
                  :options="prefectureArray"
                />
              </div>

              <!-- 条件(都道府県) select -->
              <div class="col-span-6 sm:col-span-3 md:col-span-2">
                <SelectBox
                  id="condition"
                  label="条件詳細(開発中)"
                  v-model="formData.condition"
                />
              </div>

              <!-- キーワード input -->
              <div class="col-span-6 sm:col-span-6 md:col-span-2">
                <InputLabel for="keyword" value="キーワード(開発中)" />
                <TextInput
                  id="keyword"
                  type="text"
                  class="mt-1 block w-full"
                  autocomplete=""
                  placeholder="例： 大阪 スチームサウナ"
                  v-model="formData.keyword"
                />
              </div>

              <!-- サウナ(温度) select -->
              <div class="col-span-6 md:col-span-2 mb-4">
                <label for="sauna_temperature" class="w-1/6 text-right"
                  >サウナ</label
                >
                <div class="col-span-2 flex items-center">
                  <div class="w-5/12">
                    <SelectBox
                      id="sauna_temperature_from"
                      v-model="formData.sauna_temperature_from"
                      column="temp"
                      :initialValue="initialFilters.sauna_temperature_from"
                      :options="saunaTemp"
                    />
                  </div>
                  <span class="mx-2">〜</span>
                  <div class="w-5/12">
                    <SelectBox
                      id="sauna_temperature_to"
                      v-model="formData.sauna_temperature_to"
                      column="temp"
                      :initialValue="initialFilters.sauna_temperature_to"
                      :options="saunaTemp"
                    />
                  </div>
                  <span class="mx-2 block">度</span>
                </div>
              </div>

              <!-- 水風呂(温度) select -->
              <div class="col-span-6 md:col-span-2">
                <label for="water_bath_temperature" class="w-1/6 text-right"
                  >水風呂</label
                >
                <div class="col-span-2 flex items-center">
                  <div class="w-5/12">
                    <SelectBox
                      id="water_bath_temperature_from"
                      v-model="formData.water_bath_temperature_from"
                      column="temp"
                      :initialValue="initialFilters.water_bath_temperature_from"
                      :options="waterTemp"
                    />
                  </div>
                  <span class="mx-2">〜</span>
                  <div class="w-5/12">
                    <SelectBox
                      id="water_bath_temperature_to"
                      v-model="formData.water_bath_temperature_to"
                      column="temp"
                      :initialValue="initialFilters.water_bath_temperature_to"
                      :options="waterTemp"
                    />
                  </div>
                  <span class="mx-2">度</span>
                </div>
              </div>

              <!-- 検索ボタン submit→POSTかGET -->
              <div
                class="col-span-6 md:col-span-2 bg-btn-main rounded-full text-center min-h-12 max-h-16"
              >
                <button
                  type="button"
                  class="btn btn-primary font-bold w-full h-full text-white rounded-full hover:bg-opacity-75 hover:shadow-lg active:scale-95 transition duration-150 ease-in-out"
                  @click="submitSearch"
                >
                  検索
                </button>
              </div>
            </template>
          </FormSection>

          <div class="flex justify-end text-gray mr-6 mb-6">
            <div class="mr-4">
              並び順：準備中
              <ul class="hidden">
                <!-- <li @click="sortSaunas('likes', 'desc')">いいね！多い順</li> -->
                <!-- <li @click="sortSaunas('reviews', 'desc')">サ活多い順</li> -->
                <!-- <li @click="sortSaunas('saunaTemp', 'desc')">サウナの温度が高い順</li>
                          <li @click="sortSaunas('saunaTemp', 'asc')">サウナの温度が低い順</li>
                          <li @click="sortSaunas('waterTemp', 'asc')">水風呂の温度が低い順</li>
                          <li @click="sortSaunas('waterTemp', 'desc')">水風呂の温度が高い順</li> -->
                <li @click="sortSaunas('min_fee', 'asc')">入浴料が低い順</li>
                <li @click="sortSaunas('min_fee', 'desc')">入浴料が高い順</li>
                <li @click="sortSaunas('created_at', 'desc')">新しく登録順</li>
                <li @click="sortSaunas('updated_at', 'desc')">新しく更新順</li>
              </ul>
            </div>

            <div>
              <span
                >表示: {{ saunas.from }} - {{ saunas.to }} /
                {{ saunas.total }} 件</span
              >
            </div>
          </div>

          <!-- 検索結果。一覧の表示(カードコンポーネント作成し、v-forで繰り返し処理) -->
          <div class="mb-16 px-4 py-5 bg-white sm:p-20 shadow">
            <div v-for="sauna in props.saunas.data" :key="sauna">
              <div class="block mb-8 md:flex">
                <!-- メイン画像 -->
                <div class="w-80 mr-4 mb-4">
                  <a
                    class="block rounded w-full bg-cover bg-no-repeat bg-center h-auto"
                    :href="`${baseUrl}saunas/${sauna.id}`"
                    :style="{
                      backgroundImage: `url(${
                        sauna.images_facility?.main_image_url
                          ? `${baseUrl}storage/${sauna.images_facility.main_image_url}`
                          : `${baseUrl}storage/default-images/no_image.jpg`
                      })`,
                      paddingBottom: '100%',
                    }"
                  ></a>
                </div>

                <!-- 施設情報 -->
                <div class="grid grid-cols-6 w-full">
                  <div class="col-span-6 ml-8 mb-4">
                    <h2 class="text-2xl font-bold mb-2">
                      {{ sauna.facility_name || "-" }}
                    </h2>
                    <span class="mb-2">{{
                      sauna.facility_type?.type_name || "-"
                    }}</span
                    ><span> - </span>
                    <span class="mb-2">{{ sauna.address1 || "-" }}</span>
                  </div>
                  <!-- サウナ・水風呂情報 -->
                  <div class="col-span-6 ml-8 mb-4">
                    <span class="mb-2 text-red-500 mr-8"
                      ><span
                        class="bg-red-500 text-white p-1 font-bold rounded-md"
                        >サウナ温度</span
                      >
                      : {{ sauna.sauna_info?.temperature || "-" }}</span
                    >
                    <span class="mb-2 text-blue-700"
                      ><span
                        class="bg-blue-700 text-white p-1 font-bold rounded-md"
                        >水風呂温度</span
                      >
                      : {{ sauna.water_bath?.temperature || "-" }}</span
                    >
                  </div>
                  <!-- 料金情報 -->
                  <div class="col-span-6 ml-8">
                    <p class="mb-2">最低料金: {{ sauna.min_fee || "-" }}</p>
                  </div>
                  <!-- 営業時間 -->
                  <div class="col-span-6 ml-8">
                    <h3 class="text-lg font-semibold mb-2">営業時間</h3>
                    <ul
                      v-if="
                        sauna.business_hours && sauna.business_hours.length > 0
                      "
                      class="grid grid-cols-2 md:grid-cols-3"
                    >
                      <li
                        v-for="day in sauna.business_hours"
                        :key="day.day_of_week"
                        class="col-span-1 md:col-span-1 mb-1"
                      >
                        {{ day.day_of_week }}:
                        {{ formatTime(day.opening_time) }} -
                        {{ formatTime(day.closing_time) }}
                        <span v-if="day.is_closed" class="ml-1 text-red-500"
                          >(休業)</span
                        >
                      </li>
                    </ul>
                    <p v-else class="text-gray-500">
                      営業時間情報はありません。
                    </p>
                  </div>
                </div>
              </div>
              <SectionBorder />
            </div>

            <div>
              <!-- ページネーション -->
              <nav>
                <ul class="flex justify-center space-x-2 md:space-x-6">
                  <!-- 最初のページまで遷移 -->
                  <li v-if="saunas.current_page > 1" class="w-12 h-12">
                    <a
                      @click.prevent="goToFirstPage()"
                      class="flex items-center justify-center w-full h-full bg-gray-200 hover:bg-gray-300 rounded-full"
                      >&lt;&lt;</a
                    >
                  </li>
                  <!-- 1ページ前へ遷移 -->
                  <li v-if="saunas.current_page > 1 || isLargeScreen">
                    <span v-if="isLargeScreen">
                      <a
                        @click.prevent="goToPreviousPage()"
                        class="flex items-center px-0 justify-center w-12 h-full bg-gray-200 hover:bg-gray-300 rounded-full"
                        >&lt;</a
                      >
                    </span>
                  </li>

                  <!-- 現在のページから-->
                  <div class="flex justify-center space-x-2 md:space-x-6">
                    <li
                      v-for="page in pagesToShow"
                      :key="page"
                      :class="
                        page === saunas.current_page
                          ? 'bg-blue-500 text-white'
                          : 'bg-gray-200 hover:bg-gray-300'
                      "
                      class="w-12 h-12 rounded-full"
                    >
                      <a
                        @click.prevent="paginateTo(page)"
                        class="block flex items-center justify-center rounded-full w-full h-full"
                        :class="
                          page === saunas.current_page
                            ? 'pointer-events-none'
                            : 'rounded-full'
                        "
                        >{{ page }}</a
                      >
                    </li>
                  </div>

                  <!-- 1ページ次へ遷移 -->
                  <li
                    v-if="
                      saunas.current_page < saunas.last_page || !isLargeScreen
                    "
                  >
                    <span v-if="isLargeScreen">
                      <a
                        @click.prevent="goToNextPage()"
                        class="flex items-center justify-center w-12 h-12 bg-gray-200 hover:bg-gray-300 rounded-full"
                        >&gt;</a
                      >
                    </span>
                  </li>
                  <!-- 最終ページまで遷移 -->
                  <li v-if="saunas.current_page < saunas.last_page">
                    <a
                      @click.prevent="goToLastPage()"
                      class="flex items-center justify-center w-12 h-12 bg-gray-200 hover:bg-gray-300 rounded-full"
                      >&gt;&gt;</a
                    >
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
