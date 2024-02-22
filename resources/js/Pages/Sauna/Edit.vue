<script setup>
import { ref, computed, defineProps, onMounted } from 'vue';

import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';

import AppLayout from '@/Layouts/AppLayout.vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectBox from '@/Components/SelectBox.vue';

// Laravel (app.blade.php)のCSRFトークン取得
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// axiosのデフォルトヘッダーにCSRFトークンを設定
axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;


// 親コンポーネント(Create.vue)からオブジェクト、配列の受け渡し(CompositionAPI、ObjectはArrayも含む)
const props = defineProps({
    sauna: Object,
    saunaInfo: Object,
    waterBath: Object,
    facilityTypes: Array,
    usageTypes: Array,
    prefectures: Array,
    saunaTypes: Array,
    stoveTypes: Array,
    heatTypes: Array,
    bathTypes: Array,
    waterTypes: Array,
    businessHours: Array,
    imagesFacilities: Array,
    mainImagePath: String,
    image1Path: String,
    image2Path: String,
    image3Path: String,
    image4Path: String,
    image5Path: String,
});

// Controllerからpropsとして渡ってきたデータを各名前で取得可能にする
const { sauna, saunaInfo, waterBath, businessHours, imagesFacilities,
  facilityTypes, usageTypes, prefectures, 
  saunaTypes, stoveTypes, heatTypes, 
  waterTypes, bathTypes 
} = usePage().props;

const form = useForm({
    _method: 'PUT',

    facility_name: sauna.facility_name,
    facility_type_id: sauna.facility_type_id,
    usage_type_id: sauna.usage_type_id,
    prefecture_id: sauna.prefecture_id,
    address1: sauna.address1,
    address2: sauna.address2,
    address3: sauna.address3,
    access_text: sauna.access_text,
    tel: sauna.tel,
    website_url: sauna.website_url,
    business_hours_detail: sauna.business_hours_detail,
    min_fee: sauna.min_fee,
    fee_text: sauna.fee_text,

    sauna_type_id: saunaInfo.sauna_type_id,
    stove_type_id: saunaInfo.stove_type_id,
    heat_type_id: saunaInfo.heat_type_id,
    temperature_sauna:saunaInfo.temperature,
    capacity_sauna: saunaInfo.capacity,
    additional_info_sauna: saunaInfo.additional_info,

    bath_type_id: waterBath.bath_type_id,
    water_type_id: waterBath.water_type_id,
    temperature_water: waterBath.temperature,
    capacity_water: waterBath.capacity,
    deep_water: waterBath.deep_water,
    additional_info_water: waterBath.additional_info,

    // 各曜日の入力項目(月0→日6)
    day_of_week_mon: businessHours[0].day_of_week,
    opening_time_mon: businessHours[0].opening_time,
    closing_time_mon: businessHours[0].closing_time,
    is_closed_mon: businessHours[0].is_closed,
    day_of_week_tue: businessHours[1].day_of_week,
    opening_time_tue: businessHours[1].opening_time,
    closing_time_tue: businessHours[1].closing_time,
    is_closed_tue: businessHours[1].is_closed,
    day_of_week_wed: businessHours[2].day_of_week,
    opening_time_wed: businessHours[2].opening_time,
    closing_time_wed: businessHours[2].closing_time,
    is_closed_wed: businessHours[2].is_closed,
    day_of_week_thu: businessHours[3].day_of_week,
    opening_time_thu: businessHours[3].opening_time,
    closing_time_thu: businessHours[3].closing_time,
    is_closed_thu: businessHours[3].is_closed,
    day_of_week_fri: businessHours[4].day_of_week,
    opening_time_fri: businessHours[4].opening_time,
    closing_time_fri: businessHours[4].closing_time,
    is_closed_fri: businessHours[4].is_closed,
    day_of_week_sat: businessHours[5].day_of_week,
    opening_time_sat: businessHours[5].opening_time,
    closing_time_sat: businessHours[5].closing_time,
    is_closed_sat: businessHours[5].is_closed,
    day_of_week_sun: businessHours[6].day_of_week,
    opening_time_sun: businessHours[6].opening_time,
    closing_time_sun: businessHours[6].closing_time,
    is_closed_sun: businessHours[6].is_closed,

    // 画像のアップロード
    main_image_url: imagesFacilities[0].main_image_url,
    image1_url: imagesFacilities[0].image1_url,
    image2_url: imagesFacilities[0].image2_url,
    image3_url: imagesFacilities[0].image3_url,
    image4_url: imagesFacilities[0].image4_url,
    image5_url: imagesFacilities[0].image5_url,
});

// 送信データとDB登録データの比較のため
const initialValue = {
    address2: sauna.address2,
    address3: sauna.address3,
    access_text: sauna.access_text,
    tel: sauna.tel,
    website_url: sauna.website_url,
    business_hours_detail: sauna.business_hours_detail,
    min_fee: sauna.min_fee,
    fee_text: sauna.fee_text,

    sauna_type_id: saunaInfo.sauna_type_id,
    stove_type_id: saunaInfo.stove_type_id,
    heat_type_id: saunaInfo.heat_type_id,
    temperature_sauna:saunaInfo.temperature,
    capacity_sauna: saunaInfo.capacity,
    additional_info_sauna: saunaInfo.additional_info,

    bath_type_id: waterBath.bath_type_id,
    water_type_id: waterBath.water_type_id,
    temperature_water: waterBath.temperature,
    capacity_water: waterBath.capacity,
    deep_water: waterBath.deep_water,
    additional_info_water: waterBath.additional_info,

    // 各曜日の入力項目(月0→日6)
    opening_time_mon: businessHours[0].opening_time,
    closing_time_mon: businessHours[0].closing_time,
    opening_time_tue: businessHours[1].opening_time,
    closing_time_tue: businessHours[1].closing_time,
    opening_time_wed: businessHours[2].opening_time,
    closing_time_wed: businessHours[2].closing_time,
    opening_time_thu: businessHours[3].opening_time,
    closing_time_thu: businessHours[3].closing_time,
    opening_time_fri: businessHours[4].opening_time,
    closing_time_fri: businessHours[4].closing_time,
    opening_time_sat: businessHours[5].opening_time,
    closing_time_sat: businessHours[5].closing_time,
    opening_time_sun: businessHours[6].opening_time,
    closing_time_sun: businessHours[6].closing_time,

    // 画像のアップロード
    main_image_url: imagesFacilities[0].main_image_url,
    image1_url: imagesFacilities[0].image1_url,
    image2_url: imagesFacilities[0].image2_url,
    image3_url: imagesFacilities[0].image3_url,
    image4_url: imagesFacilities[0].image4_url,
    image5_url: imagesFacilities[0].image5_url,
}
console.log(initialValue);

// 選択肢の用意Arrayと初期値設定の値selected
// 施設タイプ
const facilityTypesArray = Array.isArray(facilityTypes) ? facilityTypes : [facilityTypes];
const selectedFacilityType = ref(sauna.facility_type_id);
// 利用形態
const usageTypeArray = Array.isArray(usageTypes) ? usageTypes : [usageTypes];
const selectedUsageType = ref(sauna.usage_type_id);
// 都道府県
const prefectureArray = Array.isArray(prefectures) ? prefectures : [prefectures];
const selectedPrefecture = ref(sauna.prefecture_id);
// サウナタイプ
const saunaTypeArray = Array.isArray(saunaTypes) ? saunaTypes : [saunaTypes];
const selectedSaunaType = ref(saunaInfo.sauna_type_id);
// ストーブタイプ(radio)
const stoveTypeArray = Array.isArray(stoveTypes) ? stoveTypes : [stoveTypes];
const stoveTypeName = stoveTypes.map(type => type.type_name);
const selectedStoveType = ref(saunaInfo.stove_type_id)
// 熱源(radio)
const heatTypeArray = Array.isArray(heatTypes) ? heatTypes : [heatTypes];
const heatTypeName = heatTypes.map(type => type.source_name);
const selectedHeatType = ref(saunaInfo.heat_type_id);
// 水タイプ
const waterTypeArray = Array.isArray(waterTypes) ? waterTypes : [waterTypes];
const selectedWaterType = ref(waterBath.water_type_id);
// 水風呂タイプ
const bathTypeArray = Array.isArray(bathTypes) ? bathTypes : [bathTypes];
const selectedBathType = ref(waterBath.bath_type_id);

// 選択肢の用意 waterOptions を定義(DBで定義ではない)
const waterDepthOptions = ref([
    { id: 1, type_name: '20 ~ 40cm(すね)' },
    { id: 2, type_name: '40 ~ 60cm(ひざ)' },
    { id: 3, type_name: '60 ~ 80cm(股下)' },
    { id: 4, type_name: '80 ~ 110cm(腰)' },
    { id: 5, type_name: '110 ~ 140cm(胸)' },
    { id: 6, type_name: '140cm ~ (肩)' }
]);

// 営業時間の曜日定義
const daysOfWeek = ref({
  '月': 'mon',
  '火': 'tue',
  '水': 'wed',
  '木': 'thu',
  '金': 'fri',
  '土': 'sat',
  '日': 'sun'
});

// 選択された曜日の取得(初期選択mon)
const selectedDay = ref('mon');

// 選択された曜日をセットする関数
const selectDay = (day) => {
  selectedDay.value = day;
};

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

// DB挿入画像の配列
const imageInputs = ref([]);
const imageUrls = ref([
  { key: 'main_image_url', value: '' },
  { key: 'image1_url', value: '' },
  { key: 'image2_url', value: '' },
  { key: 'image3_url', value: '' },
  { key: 'image4_url', value: '' },
  { key: 'image5_url', value: '' },
]);

// フォームを送信するメソッド
const updateSaunaInformation = () => {
    // フォームデータ(put送信用データ))を作成
    const formData = new FormData();
    // フォームの各フィールドを更新データをformDataに追加
    Object.entries(form).forEach(([key, value]) => {
        if (value !== null && !(key.includes('_url') && value instanceof File) && value !== initialValue[key]) {
            formData.append(key, value);
        }
    });

    // Object.entries(form).forEach(([key, value]) => {
    //     // 画像フィールドの場合はファイルオブジェクトを追加
    //     if (value !== null) {
    //       if (key.includes('_url') && value instanceof File) {
    //           console.log('画像はいったよ');
    //           // ファイルが選択されている場合のみ追加
    //           if (form[key] instanceof File) {
    //               formData.append(key, form[key]);
    //           }
    //       } else {
    //           console.log('がぞう以外だよ');
    //           formData.append(key, value);
    //       }
    //     }
    // });
    console.log('put送信の中身');
    console.log(...formData.entries());

    // PUTリクエストを送信(Content-Typeを指定しない場合Laravel側で認識できなかった)
    axios.put(`/sauna_search/public/api/sauna/${props.sauna.id}`, formData, {
      headers: {
        'Content-Type': 'application/json'
      }
    })
    .then(response => {
      console.log('レスポンスデータ');
        console.log(response.data);
    })
    .catch(error => {
        console.log('エラー');
        console.error(error.response.data);
    });

    // 新規追加データをPOSTリクエストで送信(画像データの追加と変更)
    const newFormData = new FormData();
    Object.entries(form).forEach(([key, value]) => {
      if (key.includes('_url') && value instanceof File) {
          newFormData.append(key, value);
      }
    });

    console.log('post送信の中身(画像データの追加と更新)');
    console.log(...newFormData.entries());

    axios.post(`/sauna_search/public/api/sauna/${props.sauna.id}`, newFormData)
    .then(response => {
        console.log('レスポンスデータ');
        console.log(response.data);
    })
    .catch(error => {
        console.log('エラー');
        console.error(error.response.data);
    });

    // console.log(...newFormData.entries);
};

// マウント時にv-forしたinput要素(file、hidden)をimageInputsに追加
onMounted(() => {
  const fileInputs = document.querySelectorAll('input[type="file"].hidden');
  fileInputs.forEach((input, index) => {
    // input要素をimageInputsに追加
    imageInputs.value.push(input);
  });

  fetchImageUrls();
});

// const sendEmailVerification = () => {
//     verificationLinkSent.value = true;
// };

// 画像選択ボタンを押した時に、input要素を取得してクリック
const selectNewPhoto = (index) => {
  // 対応する input 要素を取得し、クリックイベントをトリガーする
  const input = imageInputs.value[index];
  if (input) {
    input.click();
  }
};

// 画像を選択した時の処理
const selectImage = (event, field, index) => {
    const file = event.target.files[0];
    form[field] = file;

    // プレビュー画像表示のためにimageUrlsのvalueを更新(blobオブジェクト生成)
    imageUrls.value[index].value = URL.createObjectURL(file);
};

// 初期画面表示時に画像のURLを取得するメソッド
const fetchImageUrls = () => {
    axios.get(`/sauna_search/public/api/sauna/${props.sauna.id}`)
        .then(response => {
            const data = response.data;
            // データから画像のURLを取得して設定
            imageUrls.value.forEach(image => {
                image.value = data[image.key];
            });
        })
        .catch(error => {
            console.error(error.response.data);
        });
};

// 画像を削除するときに実行(for対応)
const deleteImage = (field, index) => {
    form[field] = '';
    // プレビュー画像表示のためにimageUrlsのvalueを更新
    imageUrls.value[index].value = '';
};

console.log(props);
</script>

<template>
  <AppLayout title="Sauna施設情報">
      <template #header>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              サウナ施設新規追加
          </h2>
      </template>

      <div>
          <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
              <div v-if="$page.props.jetstream.canUpdateProfileInformation">

                  <div class="mb-16 px-4 py-5 bg-white sm:p-20 shadow">
                    <h2 class="text-2xl font-bold text-gray-900 text-center mb-10 ">施設を修正</h2>
                    <p>会員登録されている方であれば、誰でも施設情報を登録・更新することができます。</p>
                    <p>皆様のサウナライフをより良いものにしていただくために、より正確な情報をご提供いただけると幸いです。</p>
                    <p>すでに施設情報が登録されており、重複登録となってしまった場合は運営側で削除させていただくこともございますので、</p>
                    <p>登録前に一度施設名で検索していただき、登録がないことを確認していただけると幸いです。</p>
                  </div>

                  <!-- 施設情報 -->
                  <FormSection @submitted="updateSaunaInformation">
                      <template #title>
                          【施設情報入力】
                      </template>

                      <template #description>
                          ※わからない場合は未記入のまま進めていただいて構いません
                      </template>

                      <template #form>
                          <h3 class="col-span-6 text-xl mb-4">・施設情報</h3>
                          <!-- Facility_name input -->
                          <div class="col-span-6 sm:col-span-4">
                              <InputLabel for="facility_name" value="施設名" />
                              <TextInput
                                  id="facility_name"
                                  v-model="form.facility_name"
                                  type="text"
                                  class="mt-1 block w-full"
                                  autocomplete="facility_name"
                                  placeholder="例： サウナ＆スパ カプセルホテル 大東洋"
                              />
                              <InputError :message="form.errors.facility_name" class="mt-2" />
                          </div>

                          <!-- Saunas.Facility_types Facility_type select -->
                          <div class="col-span-6 sm:col-span-4">
                              <SelectBox 
                                id="facility_type" 
                                label="施設タイプ" 
                                v-model="form.facility_type_id"
                                :initialValue="selectedFacilityType" 
                                :options="facilityTypesArray" 
                                :error="form.errors.facility_type_id" 
                              />
                          </div>

                          <!-- Saunas.Usage_types Usage_type select -->
                          <div class="col-span-6 sm:col-span-4">
                              <SelectBox 
                                id="usage_type" 
                                label="利用形態" 
                                v-model="form.usage_type_id"
                                :initialValue="selectedUsageType" 
                                :options="usageTypeArray" 
                                :error="form.errors.usage_type_id" 
                              />
                          </div>

                          <!-- Saunas.Prefectures Prefecture select -->
                          <div class="col-span-3 sm:col-span-3">
                              <SelectBox 
                                id="prefecture_id" 
                                label="都道府県" 
                                column="name"
                                v-model="form.prefecture_id"
                                :initialValue="selectedPrefecture" 
                                :options="prefectureArray" 
                                :error="form.errors.prefecture_id" 
                              />
                          </div>

                          <!-- Saunas Address1 input -->
                          <div class="col-span-6 sm:col-span-4">
                              <InputLabel for="address1" value="住所1" />
                              <TextInput
                                  id="address1"
                                  v-model="form.address1"
                                  type="text"
                                  class="mt-1 block w-full"
                                  autocomplete="address1"
                                  placeholder="例： 大阪市北区中崎西"
                              />
                              <InputError :message="form.errors.address1" class="mt-2" />
                          </div>
                          <!-- Saunas Address2 input -->
                          <div class="col-span-6 sm:col-span-4">
                              <InputLabel for="address2" value="住所2" />
                              <TextInput
                                  id="address2"
                                  v-model="form.address2"
                                  type="text"
                                  class="mt-1 block w-full"
                                  autocomplete="address2"
                                  placeholder="例： 2-1-9"
                              />
                              <InputError :message="form.errors.address2" class="mt-2" />
                          </div>
                          <!-- Saunas Address3 input -->
                          <div class="col-span-6 sm:col-span-4">
                              <InputLabel for="address3" value="住所3" />
                              <TextInput
                                  id="address3"
                                  v-model="form. address3"
                                  type="text"
                                  class="mt-1 block w-full"
                                  autocomplete="address3"
                                  placeholder="例： 中崎観光ビル大東洋"
                              />
                              <InputError :message="form.errors.address3" class="mt-2" />
                          </div>

                          <!-- Saunas Access_text textarea -->
                          <div class="col-span-6 sm:col-span-4">
                              <InputLabel for="access_text" value="アクセス" />
                              <Textarea
                                  id="access_text"
                                  v-model="form.access_text"
                                  type="text"
                                  class="mt-1 block w-full min-h-32"
                                  autocomplete="access_text"
                                  placeholder="例：
              JR「大阪」駅、地下鉄・阪急・阪神「梅田」駅より東へ徒歩10分程度
              （HEPナビオ、ドン・キホーテから東へ200m ※「都島通り」沿い）
              ・地下鉄谷町線「中崎町」駅4番出口より徒歩3分"
                              />
                              <InputError :message="form.errors.access_text" class="mt-2" />
                          </div>

                          <!-- Saunas tel input -->
                          <div class="col-span-6 sm:col-span-4">
                              <InputLabel for="tel" value="電話番号" />
                              <TextInput
                                  id="tel"
                                  v-model="form.tel"
                                  type="text"
                                  class="mt-1 block w-full"
                                  autocomplete="tel"
                                  placeholder="例： 06-6312-7522"
                              />
                              <InputError :message="form.errors.tel" class="mt-2" />
                          </div>

                          <!-- Saunas Website_url input -->
                          <div class="col-span-6 sm:col-span-4">
                              <InputLabel for="website_url" value="Webサイト" />
                              <TextInput
                                  id="website_url"
                                  v-model="form.website_url"
                                  type="text"
                                  class="mt-1 block w-full"
                                  autocomplete="website_url"
                                  placeholder="例： http://www.daitoyo.co.jp/spa/mens/"
                              />
                              <InputError :message="form.errors.website_url" class="mt-2" />
                          </div>

                          <!-- Business_hours.Saunas business_hours hours 曜日ごとに(for) todo 初期値設定できていない-->
                          <div class="col-span-6 sm:col-span-4 pb-6 border rounded">
                            <div class="grid grid-cols-7">
                              <div v-for="(alfDay, day) in daysOfWeek" 
                                :key="day" 
                                @click="selectDay(alfDay)" 
                                class="inline-block p-4 mb-6 text-center text-gray-600 font-bold border border-gray-400 border rounded"
                                :class="[selectedDay === alfDay ? 'bg-blue-300' : 'bg-gray-200']">
                                {{ day }}
                              </div>
                            </div>

                            <div class="md:grid md:grid-cols-6 md:gap-6 ml-8">
                              <!-- 曜日ごとのビジネスアワーの入力フィールドを表示 -->
                              <div class="col-span-6"
                                  v-for="(alfDay, day) in daysOfWeek" 
                                  :key="day"
                                  v-show="selectedDay === alfDay">
                                <div>
                                  <!-- 定休日のチェックボックス -->
                                  <input 
                                    type="checkbox"
                                    v-model="form['is_closed_' + alfDay]"
                                    class="m-4 form-checkbox h-8 w-8 text-gray-600 rounded border-none focus:ring-0 shadow-none checked:bg-gray-600 bg-gray-200"
                                  >
                                  <label class="inline-block text-center text-l">定休日</label>
                                </div>

                                <div class="grid grid-cols-6 md:gap-6">
                                  <div class="col-span-2">
                                    <!-- 開始時間の入力フィールド -->
                                    <InputLabel for="'opening_time_' + alfDay" value="営業開始時間" />
                                      <TextInput
                                          id="'opening_time_' + alfDay"
                                          v-model="form['opening_time_' + alfDay]"
                                          type="time"
                                          class="mt-1 block w-full"
                                          placeholder="例： 12:00"
                                      />
                                  </div>
                                  <span class="flex justify-center items-center h-full">〜</span>
                                  <div class="col-span-2">
                                    <!-- 終了時間の入力フィールド -->
                                    <InputLabel for="'closing_time_' + alfDay" value="営業終了時間" />
                                      <TextInput
                                          id="'closing_time_' + alfDay"
                                          v-model="form['closing_time_' + alfDay]"
                                          type="time"
                                          class="mt-1 block w-full"
                                          placeholder="例： 12:00"
                                      />
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- Saunas business_hours_detail textarea -->
                          <div class="col-span-6 sm:col-span-4">
                              <InputLabel for="business_hours_detail" value="営業時間詳細" />
                              <Textarea
                                  id="business_hours_detail"
                                  v-model="form.business_hours_detail"
                                  type="text"
                                  class="mt-1 block w-full min-h-32"
                                  autocomplete="business_hours_detail"
                                  placeholder="例： 毎日営業しているが、午前10時から午後12時までは清掃のため入浴不可"
                              />
                              <InputError :message="form.errors.business_hours_detail" class="mt-2" />
                          </div>

                          <!-- Saunas min_fee input -->
                          <div class="col-span-6 sm:col-span-4">
                              <InputLabel for="min_fee" value="最低料金" />
                              <TextInput
                                  id="min_fee"
                                  v-model="form.min_fee"
                                  type="text"
                                  class="mt-1 block w-full"
                                  autocomplete="min_fee"
                                  placeholder="例： 300"
                              />
                              <InputError :message="form.errors.min_fee" class="mt-2" />
                          </div>
                          
                          <!-- Saunas fee_text textarea -->
                          <div class="col-span-6 sm:col-span-4">
                              <InputLabel for="fee_text" value="料金詳細" />
                              <Textarea
                                  id="fee_text"
                                  v-model="form.fee_text"
                                  type="text"
                                  class="mt-1 block w-full min-h-64"
                                  autocomplete="fee_text"
                                  placeholder="例：
              レギュラーコース 2,500円～
              1時間コース 1,600円
              早朝コース 2,000円

              ※深夜料金　800円
              （深夜２時以降は深夜料金がかかります（60分コース以外)"
                              />
                              <InputError :message="form.errors.fee_text" class="mt-2" />
                          </div>

                          <div class="col-span-6 py-8">
                            <div class="border-t border-gray-200" /> 
                          </div>
                          <h3 class="col-span-6 text-xl mb-4">・サウナ情報</h3>
                          
                          <!-- Sauna_infos.Saunas Sauna_type select -->
                          <div class="col-span-6 sm:col-span-4">
                              <SelectBox 
                                id="sauna_type_id" 
                                label="サウナタイプ" 
                                v-model="form.sauna_type_id"
                                :initialValue="selectedSaunaType" 
                                :options="saunaTypeArray" 
                                :error="form.errors.sauna_type_id" 
                              />
                          </div>

                          <!-- Sauna_infos.Saunas Stove_type radio -->
                          <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="stove_type_id" value="ストーブ" />
                            <div class="mt-1">

                              <label v-for="(option, index) in stoveTypeName" :key="index" class="inline-flex items-center ml-2">
                              <input
                                type="radio"
                                class="form-radio"
                                name="stove_type_id"
                                :value="index + 1"
                                v-model="form.stove_type_id"
                              />
                              <span class="ml-2">{{ option }}</span>
                              </label>
                          </div>
                          <InputError :message="form.errors.stove_id" class="mt-2" />
                          </div>

                          <!-- Sauna_infos.Saunas heat_type radio -->
                          <div class="col-span-6">
                            <InputLabel for="heat_type_id" value="熱源" />
                            <div class="mt-1">

                              <label v-for="(option, index) in heatTypeName" :key="index" class="inline-flex items-center ml-2">
                              <input
                                type="radio"
                                class="form-radio"
                                name="heat_type_id"
                                :value="index + 1"
                                v-model="form.heat_type_id"
                              />
                              <span class="ml-2">{{ option }}</span>
                              </label>
                          </div>
                          <InputError :message="form.errors.heat_type_id" class="mt-2" />
                          </div>

                          <!-- Sauna_infos.Saunas temperature input -->
                          <div class="col-span-6">
                            <div class="grid grid-cols-6 gap-6">
                              <div class="col-span-3 md:col-span-2">
                                <InputLabel for="temperature" value="温度" />
                                <div class="relative">
                                  <TextInput
                                      id="temperature"
                                      v-model="form.temperature_sauna"
                                      type="text"
                                      class="mt-1 block w-full pr-12 col-span-1"
                                      autocomplete="temperature"
                                      placeholder="95"
                                  />
                                  <span class="absolute inset-y-0 right-0 pr-3 flex items-center">度</span>
                                </div>
                                <InputError :message="form.errors.temperature_sauna" class="mt-2" />
                              </div>
                            </div>
                          </div>

                          <!-- Sauna_infos.Saunas capacity input -->
                          <div class="col-span-6">
                            <div class="grid grid-cols-6 gap-6">
                              <div class="col-span-3 md:col-span-2">
                                <InputLabel for="capacity" value="収容人数" />
                                <div class="relative">
                                  <TextInput
                                      id="capacity"
                                      v-model="form.capacity_sauna"
                                      type="text"
                                      class="mt-1 block w-full pr-12 col-span-1"
                                      autocomplete="capacity"
                                      placeholder="10"
                                  />
                                  <span class="absolute inset-y-0 right-0 pr-3 flex items-center">人</span>
                                </div>
                                <InputError :message="form.errors.capacity_sauna" class="mt-2" />
                              </div>
                            </div>
                          </div>
                          <!-- Sauna_infos.Saunas additional_info textarea -->
                          <div class="col-span-6 sm:col-span-4">
                              <InputLabel for="additional_info" value="補足" />
                              <Textarea
                                  id="additional_info"
                                  v-model="form.additional_info_sauna"
                                  type="text"
                                  class="mt-1 block w-full min-h-32"
                                  autocomplete="additional_info"
                                  placeholder="例： タオルに水を染み込ませて持ち込まないローカルなルール"
                              />
                              <InputError :message="form.errors.additional_info_sauna" class="mt-2" />
                          </div>

                          <div class="col-span-6 py-8">
                            <div class="border-t border-gray-200" /> 
                          </div>
                          <h3 class="col-span-6 text-xl mb-4">・水風呂情報</h3>


                          <!-- Water_baths.Saunas bath_type select -->
                          <div class="col-span-6 sm:col-span-4">
                              <SelectBox 
                                id="bath_type_id" 
                                label="タイプ" 
                                v-model="form.bath_type_id"
                                :initialValue="selectedBathType" 
                                :options="bathTypeArray" 
                                :error="form.errors.bath_type_id" 
                              />
                          </div>

                          <!-- Water_baths.Saunas water_type select -->
                          <div class="col-span-6 sm:col-span-4">
                              <SelectBox 
                                id="water_type_id" 
                                label="水" 
                                v-model="form.water_type_id"
                                :initialValue="selectedWaterType" 
                                :options="waterTypeArray" 
                                :error="form.errors.water_type_id" 
                              />
                          </div>

                          <!-- Water_baths.Saunas temperature input -->
                          <div class="col-span-6">
                            <div class="grid grid-cols-6 gap-6">
                              <div class="col-span-3 md:col-span-2">
                                <InputLabel for="temperature" value="温度" />
                                <div class="relative">
                                  <TextInput
                                      id="temperature"
                                      v-model="form.temperature_water"
                                      type="text"
                                      class="mt-1 block w-full pr-12 col-span-1"
                                      autocomplete="temperature"
                                      placeholder="15"
                                  />
                                  <span class="absolute inset-y-0 right-0 pr-3 flex items-center">度</span>
                                </div>
                                <InputError :message="form.errors.temperature_water" class="mt-2" />
                              </div>
                            </div>
                          </div>

                          <!-- Water_baths.Saunas capacity input -->
                          <div class="col-span-6">
                            <div class="grid grid-cols-6 gap-6">
                              <div class="col-span-3 md:col-span-2">
                                <InputLabel for="capacity" value="収容人数" />
                                <div class="relative">
                                  <TextInput
                                      id="capacity"
                                      v-model="form.capacity_water"
                                      type="text"
                                      class="mt-1 block w-full pr-12 col-span-1"
                                      autocomplete="capacity"
                                      placeholder="5"
                                  />
                                  <span class="absolute inset-y-0 right-0 pr-3 flex items-center">人</span>
                                </div>
                                <InputError :message="form.errors.capacity_water" class="mt-2" />
                              </div>
                            </div>
                          </div>

                          <!-- Water_baths.Saunas deep_water input -->
                          <div class="col-span-6 sm:col-span-4">
                              <SelectBox 
                                id="deep_water" 
                                label="水深"
                                v-model="form.deep_water"
                                :initialValue="selectedSaunaType" 
                                :options="waterDepthOptions" 
                                :error="form.errors.deep_water" 
                              />
                          </div>

                          <!-- Water_baths.Saunas additional_info textarea -->
                          <div class="col-span-6 sm:col-span-4">
                              <InputLabel for="additional_info" value="補足" />
                              <Textarea
                                  id="additional_info"
                                  v-model="form.additional_info_water"
                                  type="text"
                                  class="mt-1 block w-full min-h-32"
                                  autocomplete="additional_info"
                                  placeholder="例： 水風呂ではなく掛け流しタイプ"
                              />
                              <InputError :message="form.errors.access_text_water" class="mt-2" />
                          </div>

                          <div class="col-span-6 py-8">
                          <div class="border-t border-gray-200" /> 
                        </div>
                        <h3 class="col-span-6 text-xl mb-4">・画像登録</h3>
                      <!-- 画像アップロード 6枚(main1枚と1~5のimage) -->
                      <div class="col-span-6">
                            <!-- 施設メイン画像のinput -->
                            <div class="grid grid-cols-6 gap-6">

                                <div v-for="(image, index) in imageUrls" key="index" class="col-span-6 md:col-span-2 sm:col-span-3">
                                  <input
                                    :ref="`imageInputs${index}`"
                                    type="file"
                                    class="hidden"
                                    @change="selectImage($event, image.key, index)"
                                  >

                                  <InputLabel :for="`imageInput${image.key}`" value="サウナ画像" />

                                  <div v-if="image.value" class="mt-2">
                                    <template v-if="image.value.startsWith('blob:')">
                                      <!-- 画像を選択して追加した場合の処理 -->
                                      <span class="block rounded w-full bg-cover bg-no-repeat bg-center" 
                                            :style="{ backgroundImage: `url(${image.value})`, paddingBottom: '100%' }"></span>
                                    </template>
                                    <template v-else>
                                      <!-- DBから取得した画像データの場合の処理 -->
                                      <span class="block rounded w-full bg-cover bg-no-repeat bg-center" 
                                            :style="{ backgroundImage: `url(http://localhost/storage/${image.value})`, paddingBottom: '100%' }"></span>
                                    </template>
                                  </div>
                                  <div v-else class="mt-2 w-full">
                                    <!-- 画像が空の場合に表示する代替の画像 -->
                                    <img :src="`http://localhost/storage/default-images/no_image.jpg`" 
                                        :alt="sauna.facility_name" 
                                        class="rounded w-full object-cover">
                                  </div>

                                  <!-- 施設画像追加・削除ボタン -->
                                  <SecondaryButton class="mt-2 mr-2" type="button" @click.prevent="selectNewPhoto(index)">
                                      画像を選択
                                  </SecondaryButton>

                                  <SecondaryButton
                                      v-if="image.value"
                                      type="button"
                                      class="mt-2 bg-red-300"
                                      @click.prevent="deleteImage(image.key, index)"
                                  >
                                      削除
                                  </SecondaryButton>

                                  <InputError :message="form.errors.photo" class="mt-2" />
                                </div>
                            </div>
                        </div>

                      </template>

                      <template #actions>
                          <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                              Saved.
                          </ActionMessage>

                          <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                              Save
                          </PrimaryButton>
                      </template>
                  </FormSection>

    
              </div>
          </div>
        </div>
  </AppLayout>
</template>
