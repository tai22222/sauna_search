<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, computed, defineProps, onMounted } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import axios from "axios";

import SectionBorder from "@/Components/SectionBorder.vue";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Textarea from "@/Components/Textarea.vue";

// Laravel (app.blade.php)のCSRFトークン取得
const csrfToken = document
  .querySelector('meta[name="csrf-token"]')
  .getAttribute("content");

// axiosのデフォルトヘッダーにCSRFトークンを設定
axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken;

// ベースURLの設定
const baseUrl = import.meta.env.VITE_APP_BASE_URL;
const baseUrI = import.meta.env.VITE_APP_BASE_URI;

// 親コンポーネント(Create.vue)からオブジェクト、配列の受け渡し(CompositionAPI、ObjectはArrayも含む)
const props = defineProps({
  sauna: Object,
  auth: Object,
  favoritesCount: Number,
  isFavorited: Boolean,
});
console.log(props);
const { sauna, auth } = usePage().props;

// propsから取得したデータが空の場合は指定の文字列を表示する
// 住所2
const saunaAddress2 = computed(() => {
  return sauna.address2 ? sauna.address2 : "";
});

// 住所3
const saunaAddress3 = computed(() => {
  return sauna.address3 ? sauna.address3 : "";
});

const address =
  sauna.prefecture.name + sauna.address1 + sauna.address2 + sauna.address3;

// 料金詳細の\r\n部分をbrタグに変換
const formattedFeeText = computed(() => {
  return sauna.fee_text ? sauna.fee_text.replace(/\r\n/g, "<br>") : "";
});

// 定休日の有無
const businessHourIsClosed = computed(() => {
  return sauna.business_hours && sauna.business_hours.is_closed
    ? "あり"
    : "なし";
});

// サウナの温度
const saunaTemperature = computed(() => {
  return sauna.sauna_info && sauna.sauna_info.temperature
    ? sauna.sauna_info.temperature
    : "-";
});

// サウナの収容人数
const saunaCapacity = computed(() => {
  return sauna.sauna_info && sauna.sauna_info.capacity
    ? sauna.sauna_info.capacity
    : "-";
});

// サウナの追加情報
const saunaAdditionalInfo = computed(() => {
  return sauna.sauna_info && sauna.sauna_info.additional_info
    ? sauna.sauna_info.additional_info
    : "-";
});

// サウナのタイプ
const saunaType = computed(() => {
  return sauna.sauna_info.sauna_type && sauna.sauna_info.sauna_type.type_name
    ? sauna.sauna_info.sauna_type.type_name
    : "";
});

// ストーブのタイプ
const stoveType = computed(() => {
  return sauna.sauna_info.stove_type && sauna.sauna_info.stove_type.type_name
    ? sauna.sauna_info.stove_type.type_name
    : "";
});

// 熱源のタイプ
const heatType = computed(() => {
  return sauna.sauna_info.heat_type && sauna.sauna_info.heat_type.source_name
    ? sauna.sauna_info.heat_type.source_name
    : "";
});

// 水風呂の温度
const waterBathTemperature = computed(() => {
  return sauna.water_bath && sauna.water_bath.temperature
    ? sauna.water_bath.temperature
    : "-";
});

// 水風呂の収容人数
const waterBathCapacity = computed(() => {
  return sauna.water_bath && sauna.water_bath.capacity
    ? sauna.water_bath.capacity
    : "-";
});

// 水風呂の水深
const waterDepth = computed(() => {
  return sauna.water_bath && sauna.water_bath.water_deep
    ? sauna.water_bath.water_deep
    : "";
});

// 水風呂の水深
const waterBathAdditionalInfo = computed(() => {
  return sauna.water_bath && sauna.water_bath.additional_info
    ? sauna.water_bath.additional_info
    : "";
});

// 水の種類
const waterType = computed(() => {
  return sauna.water_bath.water_type && sauna.water_bath.water_type.type_name
    ? sauna.water_bath.water_type.type_name
    : "";
});

// 水風呂の種類
const bathType = computed(() => {
  return sauna.water_bath.bath_type && sauna.water_bath.bath_type.type_name
    ? sauna.water_bath.bath_type.type_name
    : "";
});

//営業時間のフォーマット変換(DBから渡ってくるのはH:i:sなのでH:iに変更、24時を回っている時は翌○○時の記述に変換)
const formattedBusinessHours = computed(() => {
  let formattedArray = []; // 整形されたデータを保持するための配列
  if (sauna.business_hours) {
    for (const index in sauna.business_hours) {
      if (
        sauna.business_hours[index].opening_time ||
        sauna.business_hours[index].closing_time
      ) {
        let openingTime = sauna.business_hours[index].opening_time.substr(0, 5);
        let closingTime = sauna.business_hours[index].closing_time.substr(0, 5);
        let nextDay = "";

        // 終了時間が開始時間より前の場合、"翌"を追加
        if (
          sauna.business_hours[index].closing_time <
          sauna.business_hours[index].opening_time
        ) {
          nextDay = "翌";
        }

        let formattedItem = {
          ...sauna.business_hours[index],
          opening_time: openingTime,
          closing_time: `${nextDay}${closingTime}`,
        };
        formattedArray.push(formattedItem);
      }
    }
  }
  return formattedArray;
});

// 施設画像
const images = [
  { image_url: sauna.images_facility.main_image_url, alt: "メイン画像" },
  { image_url: sauna.images_facility.image1_url, alt: "サウナ施設関連画像" },
  { image_url: sauna.images_facility.image2_url, alt: "サウナ施設関連画像" },
  { image_url: sauna.images_facility.image3_url, alt: "サウナ施設関連画像" },
  { image_url: sauna.images_facility.image4_url, alt: "サウナ施設関連画像" },
  { image_url: sauna.images_facility.image5_url, alt: "サウナ施設関連画像" },
];

const writeBoard = ref(false);
// サ活の投稿をクリック時に投稿画面表示
const openWriteReview = () => {
  //ログインしているかどうかの判定
  if (!auth.user) {
    Inertia.visit("/login");
  } else {
    writeBoard.value = true;
  }
};

const closeWriteBoard = () => {
  writeBoard.value = false;
};

const beforeEnter = (el) => {
  el.style.opacity = 0;
};

const enter = (el, done) => {
  el.offsetHeight;
  el.style.transition = "opacity 0.5s ease";
  el.style.opacity = 1;
  done();
};

const leave = (el, done) => {
  el.style.opacity = 0;
  el.style.transition = "opacity 0.5s ease";
  setTimeout(() => done(), 500);
};

const photoInput = ref(null);
const photoPreview = ref(null);

// 画像選択ボタンを押した時に、input要素を取得してクリック
const selectNewPhoto = () => {
  photoInput.value.click();
};

// 写真が変更された時(プレビュー時点)
const updatePhotoPreview = () => {
  const photo = photoInput.value.files[0];
  // clearPhotoFileInput実行時
  if (!photo) return;
  const reader = new FileReader();
  reader.onload = (e) => {
    // data:オブジェクトとして格納
    photoPreview.value = e.target.result;
  };
  // FileまたはBlobオブジェクトを読み込み、読み込まれたデータをBase64エンコーディングされた文字列としてresultプロパティに格納
  reader.readAsDataURL(photo);
};

// 画像のプレビュー表示をクリア(form送信前)
const clearPhotoFileInput = () => {
  if (photoInput.value.value) {
    photoInput.value.value = null;
    photoPreview.value = null;
  }
};

// フォームデータ(画像以外のリアクティブなデータを明示)
const { data, post, processing, errors } = useForm({
  user_id: auth.user.id,
  sauna_id: sauna.id,

  visited_date: "",
  title: "",
  content: "",
});

// フォームデータの送信ボタンクリックでルーティング経由のControllerへ
const createReview = () => {
  // 画像データの送信がある場合はuseFormフックのpostメソッドでは送れないため、FormDataオブジェクトに格納して送信する
  const formData = new FormData();

  // useFormで定義した画像以外のデータをFormDataオブジェクトに追加
  for (const key in data) {
    formData.append(key, data[key]);
  }

  // フォームデータに各フィールド(FormSectionにないデータ)を追加
  formData.append("user_id", auth.user.id);
  formData.append("sauna_id", sauna.id);

  if (photoInput.value.files[0]) {
    formData.append("review_image", photoInput.value.files[0]);
  }

  // フォームデータの中身を確認
  // console.log('formData');
  // for (let [key, value] of formData.entries()) {
  // console.log(key, value);
  // }

  Inertia.post(route("review.store", sauna.id), formData, {
    onSuccess: () => {
      console.log("送信成功");
    },
    onError: () => {
      console.log("送信失敗");
    },
    forceFormData: true,
  });
};

const count = ref(data.content);
// 文字数のカウント
const updateCount = () => {
  count.value = data.content.length;
};

const isFavorited = ref(props.isFavorited);
const favoritesCount = ref(props.favoritesCount);
// お気に入りの追加。解除のメソッド
const toggleFavorite = async () => {
  try {
    // UIを即時更新
    isFavorited.value ? favoritesCount.value-- : favoritesCount.value++;
    const previousIsFavorited = isFavorited.value;
    isFavorited.value = !isFavorited.value;
    console.log(favoritesCount);
    console.log(isFavorited);
    // APIリクエストの送信
    const response = await axios.post(
      `${baseUrI}api/saunas/${sauna.id}/toggle-favorite`,
      { sauna_id: sauna.id, user_id: auth.user.id },
      { withCredentials: true }
    );
    favoritesCount.value = response.data.favoritesCount;
    console.log(favoritesCount);
    console.log(isFavorited);
  } catch (error) {
    console.error("お気に入りのトグルに失敗しました");
    // エラーが発生した場合は、UIの更新を元に戻す
    favoritesCount.value = previousIsFavorited
      ? favoritesCount.value + 1
      : favoritesCount.value - 1;
    isFavorited.value = previousIsFavorited;
  }
};

// 編集ページへ遷移
const goToEditPage = () => {
  // ログインしているかどうかを判定
  if (!auth.user) {
    // ログインしていない場合はログインページにリダイレクト
    Inertia.visit("/login");
  } else {
    // ログインしている場合は編集ページ（例）に遷移
    Inertia.visit(`${baseUrl}saunas/edit/${sauna.id}`);
  }
};

// 戻るボタン
const goBack = () => {
  window.history.back();
};

const map = ref(null);

// Google Maps API スクリプトをロードする関数
const loadGoogleMapsScript = () => {
  return new Promise((resolve, reject) => {
    if (window.google) {
      resolve(window.google);
    } else {
      const script = document.createElement("script");
      script.src = `https://maps.googleapis.com/maps/api/js?key=${
        import.meta.env.VITE_GOOGLE_MAPS_API_KEY
      }`;
      script.async = true;
      document.head.appendChild(script);
      script.onload = () => resolve(window.google);
      script.onerror = (error) => reject(error);
    }
  });
};

// 住所から地図を表示する関数
const showMapFromAddress = async (address, google) => {
  const response = await fetch(
    `https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(
      address
    )}&key=${import.meta.env.VITE_GOOGLE_MAPS_API_KEY}`
  );
  const data = await response.json();

  if (data.results.length > 0) {
    const location = data.results[0].geometry.location;
    map.value = new google.maps.Map(document.getElementById("map"), {
      center: location,
      zoom: 15,
    });

    // マーカーを地図上に追加
    const marker = new google.maps.Marker({
      position: location,
      map: map.value,
      title: "サウナの位置",
    });
  }
};

onMounted(async () => {
  try {
    const google = await loadGoogleMapsScript();
    await showMapFromAddress(address, google);
  } catch (error) {
    console.error("Google Maps APIの読み込みに失敗しました", error);
  }
});
</script>

<template>
  <AppLayout title="Sauna">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        施設詳細ページ
      </h2>
    </template>

    <div class="relative">
      <transition @before-enter="beforeEnter" @enter="enter" @leave="leave">
        <div
          v-if="writeBoard"
          @click.prevent="closeWriteBoard()"
          class="absolute bg-gray-800/40 h-full w-full"
        >
          <div @click.stop class="max-w-5xl mx-auto pt-24">
            <div>
              <FormSection @submitted="createReview">
                <template #title> </template>

                <template #description> </template>

                <template #form>
                  <h3
                    class="col-span-6 text-xl font-black mb-4 text-center py-4"
                  >
                    {{ sauna.facility_name }}
                  </h3>
                  <!-- 整い日 -->
                  <div class="col-span-3">
                    <InputLabel for="visited_date" value="整い日" />
                    <TextInput
                      id="visited_date"
                      v-model="data.visited_date"
                      type="date"
                      class="mt-1 block w-full"
                    />
                    <InputError class="mt-2" />
                  </div>

                  <!-- タイトル -->
                  <div class="col-span-6">
                    <InputLabel for="title" value="タイトル" />
                    <TextInput
                      id="title"
                      v-model="data.title"
                      type="text"
                      class="mt-1 block w-full"
                      placeholder="施設の綺麗さ、価格。サウナの温度が最高"
                    />
                    <InputError class="mt-2" />
                  </div>

                  <!-- レビュー詳細 -->
                  <div class="col-span-6">
                    <InputLabel for="content" value="サ活 / レビュー" />
                    <Textarea
                      id="content"
                      v-model="data.content"
                      @input="updateCount"
                      type="textarea"
                      class="mt-1 block w-full min-h-80"
                      placeholder="友達のおすすめのサウナに初サ活！
仕事終わりの20時くらいに訪れましたが、思ったより混んでいなかった。
サウナの温度は95度と自分の中のベストだったので、ポイントアップ！

1時間に1回スタッフさんによるロウリュウがあり
105度くらいまで上がりました。

水風呂は10度くらいで、その後近くのベンチで整い。
今回も3セットやり大大大満足。

お風呂を出てからせっかくなのでご飯を！
友達のおすすめが自分のおすすめにもなりました！

"
                    />
                    <div>
                      <span
                        v-if="count"
                        :class="{ 'text-red-500': data.content.length > 500 }"
                        >{{ count }}</span
                      ><span v-else-if="!count">0</span> / 500文字
                    </div>
                    <InputError class="mt-2" />
                  </div>

                  <!-- 画像 -->
                  <div class="col-span-1">画像</div>
                  <div class="col-span-2">
                    <input
                      type="file"
                      ref="photoInput"
                      class="hidden"
                      @change="updatePhotoPreview"
                    />

                    <div v-if="photoPreview">
                      <span
                        class="block rounded w-full bg-cover bg-no-repeat bg-center"
                        :style="{
                          backgroundImage: `url(${photoPreview})`,
                          paddingBottom: '100%',
                        }"
                      />
                    </div>

                    <!-- 施設画像追加・削除ボタン -->
                    <SecondaryButton
                      class="mt-2 mr-2"
                      type="button"
                      @click.prevent="selectNewPhoto()"
                    >
                      画像を選択
                    </SecondaryButton>

                    <SecondaryButton
                      v-if="photoPreview"
                      type="button"
                      class="mt-2"
                      bgColor="bg-red-300"
                      @click.prevent="clearPhotoFileInput()"
                    >
                      削除
                    </SecondaryButton>
                    <!-- <InputError :message="form.errors.photo" class="mt-2" /> -->
                  </div>
                </template>

                <template #actions>
                  <button
                    class="inline-flex items-center px-12 py-3 bg-white border-2 border-gray-500 rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300/70 hover:text-white focus:bg-gray-500 focus:text-white active:bg-gray-500/70 active:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                  >
                    サ活の投稿
                  </button>
                </template>
              </FormSection>
            </div>
          </div>
        </div>
      </transition>

      <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
          <div v-if="$page.props.jetstream.canUpdateProfileInformation">
            <div class="flex justify-end mr-6 mb-6">
              <div
                class="px-4 py-2 text-gray font-bold border-b-4 border-gray-900 hover:border-gray-600/90 rounded-lg"
              >
                <a
                  @click.prevent="goToEditPage()"
                  class="flex items-center justify-center w-full"
                  href="#"
                  >施設情報の編集</a
                >
              </div>
            </div>

            <div class="mb-16 px-4 py-5 bg-white sm:p-20 shadow rounded-2xl">
              <div class="flex justify-between mb-8">
                <div class="flex px-2 md:px-4 py-2">
                  <div class="pr-8">
                    <button
                      class="px-4 py-2 text-gray hover:bg-blue-400 hover:text-white font-bold border-2 rounded-lg button-transition"
                    >
                      ツイート
                    </button>
                  </div>
                  <div>
                    <button
                      class="px-4 py-2 text-gray hover:bg-blue-400 hover:text-white font-bold border-2 rounded-lg button-transition"
                    >
                      シェア
                    </button>
                  </div>
                </div>

                <div class="flex px-2 md:px-4 py-2">
                  <div class="pr-8">
                    <button
                      class="px-4 py-2 font-bold border-2 border-blue-600 rounded-lg"
                      :class="
                        isFavorited
                          ? 'text-white bg-blue-600 border-blue-600 hover:text-blue-600 hover:bg-white button-transition'
                          : 'text-blue-600 hover:bg-blue-600 hover:text-white border-blue-600 button-transition'
                      "
                      @click="toggleFavorite"
                    >
                      お気に入り <span>{{ favoritesCount }}</span>
                    </button>
                  </div>
                  <div>
                    <button
                      @click.prevent="openWriteReview()"
                      class="px-4 py-2 bg-gray-800 hover:bg-gray-800/80 text-white font-bold rounded-lg"
                    >
                      サ活の投稿
                    </button>
                  </div>
                </div>
              </div>
              <div class="grid grid-cols-6 gap-2">
                <div class="col-span-6 text-3xl font-bold">
                  {{ sauna.facility_name }}
                </div>
                <div class="col-span-6">
                  {{ sauna.facility_type.type_name }}
                </div>
                <div class="col-span-2 mb-4">
                  {{ sauna.prefecture.name }} - {{ sauna.address1 }}
                </div>
                <!-- <div class="col-span-6">
                            <button>Twitterツイート</button>
                            <button>フェイスブックシェア</button>
                          </div> -->
                <div class="col-span-6">
                  <ul class="grid grid-cols-4 text-xl">
                    <li>
                      <a href="" class="border-b-4 border-blue-200">施設情報</a>
                    </li>
                    <li><a href="">サ活(準備中)</a></li>
                    <li><a href="">サウナ飯(準備中)</a></li>
                  </ul>
                </div>
              </div>

              <SectionBorder />

              <div class="grid grid-cols-2 gap-16 mb-12">
                <div
                  id="saunaInfo"
                  class="md:col-span-1 col-span-2 px-6 py-6 border-2 rounded-xl text-center"
                >
                  <div class="mb-2 text-xl font-bold text-red-500">
                    サウナ室
                  </div>
                  <div class="mb-2">
                    温度<span class="text-3xl text-red-500">{{
                      saunaTemperature
                    }}</span
                    ><span class="text-red-500">度</span>
                  </div>
                  <div class="mb-2">収容人数： {{ saunaCapacity }} 人</div>
                  <div class="mb-4">
                    <ul class="flex justify-center text-white">
                      <li
                        v-if="saunaType"
                        class="bg-gray-400 px-2 py-1 font-bold border-2 rounded-2xl"
                      >
                        {{ saunaType }}
                      </li>
                      <li
                        v-if="stoveType"
                        class="bg-gray-400 px-2 py-1 font-bold border-2 rounded-2xl"
                      >
                        {{ stoveType }}
                      </li>
                      <li
                        v-if="heatType"
                        class="bg-gray-400 px-2 py-1 font-bold border-2 rounded-2xl"
                      >
                        {{ heatType }}
                      </li>
                    </ul>
                  </div>
                  <div>{{ saunaAdditionalInfo }}</div>
                </div>
                <div
                  id="waterBath"
                  class="md:col-span-1 col-span-2 px-6 py-6 border-2 rounded-xl text-center"
                >
                  <div class="mb-2 text-xl font-bold text-blue-500">
                    水風呂情報
                  </div>
                  <div class="mb-2">
                    温度<span class="text-3xl text-blue-500">{{
                      waterBathTemperature
                    }}</span
                    ><span class="text-blue-500">度</span>
                  </div>
                  <div class="mb-2">収容人数： {{ waterBathCapacity }} 人</div>
                  <div class="mb-4">
                    <ul class="flex justify-center text-white">
                      <li
                        v-if="waterType"
                        class="bg-gray-400 px-2 py-1 font-bold border-2 rounded-2xl"
                      >
                        {{ waterType }}
                      </li>
                      <li
                        v-if="bathType"
                        class="bg-gray-400 px-2 py-1 font-bold border-2 rounded-2xl"
                      >
                        {{ bathType }}
                      </li>
                      <li
                        v-if="waterDepth"
                        class="bg-gray-400 px-2 py-1 font-bold border-2 rounded-2xl"
                      >
                        {{ waterDepth }}
                      </li>
                    </ul>
                  </div>
                  <div>{{ waterBathAdditionalInfo }}</div>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-6 mb-12">
                <div id="saunas" class="col-span-2">基本情報</div>
                <div class="col-span-2 md:col-span-1 mr-6">
                  <div class="w-full">
                    <span
                      class="block rounded w-full bg-cover bg-no-repeat bg-center mb-6"
                      :style="{
                        backgroundImage: `url(${baseUrl}storage/${sauna.images_facility.main_image_url})`,
                        paddingBottom: '100%',
                      }"
                    ></span>
                  </div>
                  <div class="w-full mb-4">
                    <!-- マップ -->
                    <div id="map" class="w-full h-96"></div>
                  </div>
                </div>
                <div class="col-span-2 md:col-span-1">
                  <div class="grid grid-cols-9 gap-6">
                    <div class="col-span-2 font-bold">施設名</div>
                    <div class="col-span-7">
                      {{ sauna.facility_name }}
                    </div>
                    <div class="col-span-9 border-t border-gray-200" />
                    <div class="col-span-2 font-bold">施設タイプ</div>
                    <div class="col-span-7">
                      {{ sauna.facility_type.type_name }}
                    </div>
                    <div class="col-span-9 border-t border-gray-200" />
                    <div class="col-span-2 font-bold">住所</div>
                    <div class="col-span-7">
                      {{ sauna.prefecture.name }} {{ sauna.address1 }}
                      {{ saunaAddress2 }} {{ saunaAddress3 }}
                    </div>
                    <div class="col-span-9 border-t border-gray-200" />
                    <div class="col-span-2 font-bold">アクセス</div>
                    <div class="col-span-7">
                      {{ sauna.access_text }}
                    </div>
                    <div class="col-span-9 border-t border-gray-200" />
                    <div class="col-span-2 font-bold">TEL</div>
                    <div class="col-span-7">
                      {{ sauna.tel }}
                    </div>
                    <div class="col-span-9 border-t border-gray-200" />
                    <div class="col-span-2 font-bold">HP</div>
                    <div class="col-span-7 break-words">
                      <a :href="sauna.website_url">{{ sauna.website_url }}</a>
                    </div>
                    <div class="col-span-9 border-t border-gray-200" />
                    <div class="col-span-2 font-bold">定休日</div>
                    <div class="col-span-7">
                      {{ businessHourIsClosed }}
                    </div>
                    <div class="col-span-9 border-t border-gray-200" />
                    <div class="col-span-2 font-bold">営業時間</div>
                    <div class="col-span-7">
                      <ul v-for="hour in formattedBusinessHours" :key="hour.id">
                        <li>
                          {{ hour.day_of_week }}曜日 ：
                          {{ hour.opening_time }} ~ {{ hour.closing_time }}
                        </li>
                      </ul>
                    </div>
                    <div class="col-span-9 border-t border-gray-200" />
                    <div class="col-span-2 font-bold">料金</div>
                    <div class="col-span-7">
                      <div v-html="formattedFeeText"></div>
                    </div>
                  </div>
                </div>
              </div>

              <SectionBorder />

              <div class="grid grid-cols-6 gap-4 mb-12">
                <div class="col-span-6 mb-6">写真ギャラリー</div>
                <div
                  v-for="image in images"
                  :key="image"
                  class="sm:col-span-3 md:col-span-2 col-span-6"
                >
                  <div v-if="image.image_url">
                    <p>{{ image.alt }}</p>
                    <span
                      class="block rounded w-full bg-cover bg-no-repeat bg-center"
                      :style="{
                        backgroundImage: `url(${baseUrl}storage/${image.image_url})`,
                        paddingBottom: '100%',
                      }"
                    ></span>
                  </div>
                </div>
              </div>
              <div
                class="w-32 bg-gray-700 text-white px-4 py-2 rounded-lg text-center"
              >
                <button @click="goBack" class="block w-full h-full">
                  戻る
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
