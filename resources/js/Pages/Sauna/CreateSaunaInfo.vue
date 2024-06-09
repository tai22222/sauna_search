<script setup>
import { ref, defineProps, onMounted  } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia'

import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectBox from '@/Components/SelectBox.vue';

// バリデーション
import { isValidText, isValidMax, isValidPhone, isValidUrl, isValidNumber, isValidImageSize, isValidImageType} from '@/Utils/validators';

// Laravel (app.blade.php)のCSRFトークン取得
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// axiosのデフォルトヘッダーにCSRFトークンを設定
axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

// ベースURLの設定
const baseUrl = import.meta.env.VITE_APP_BASE_URL; 

// 親コンポーネント(Create.vue)からオブジェクト、配列の受け渡し(CompositionAPI、ObjectはArrayも含む)
const props = defineProps({
    user: Object,
    sauna: Object,
    facilityType: Array,
    usageType: Array,
    prefecture: Array,
    saunaType: Array,
    stoveType: Array,
    heatType: Array,
    bathType: Array,
    waterType: Array,

});

// フォームの各入力項目に対応するPOSTデータ
// (saunasテーブル、saunas_infosテーブル、water_bathsテーブル、images_facilitiesテーブル、business_hoursテーブル)
const form = useForm({
    _method: 'POST',
    user_id: props.user.id,

    // サウナ情報に関する入力項目
    facility_name: '',
    facility_type_id:'',
    usage_type_id:'',
    prefecture_id:'',
    address1:'',
    address2:'',
    address3:'',
    access_text:'',
    tel:'',
    website_url:'',
    business_hours_detail:'',
    min_fee:'',
    fee_text:'',

    // サウナに関する入力項目
    sauna_type_id:'',
    stove_type_id:'',
    heat_type_id:'',
    temperature_sauna:'',
    capacity_sauna:'',
    additional_info_sauna:'',

    // 水風呂に関する入力項目
    bath_type_id:'',
    water_type_id:'',
    temperature_water:'',
    capacity_water:'',
    deep_water:'',
    additional_info_water:'',

    // 各曜日の入力項目
    day_of_week_mon: '',
    opening_time_mon: '',
    closing_time_mon: '',
    is_closed_mon: false,
    day_of_week_tue: '',
    opening_time_tue: '',
    closing_time_tue: '',
    is_closed_tue: false,
    day_of_week_wed: '',
    opening_time_wed: '',
    closing_time_wed: '',
    is_closed_wed: false,
    day_of_week_thu: '',
    opening_time_thu: '',
    closing_time_thu: '',
    is_closed_thu: false,
    day_of_week_fri: '',
    opening_time_fri: '',
    closing_time_fri: '',
    is_closed_fri: false,
    day_of_week_sat: '',
    opening_time_sat: '',
    closing_time_sat: '',
    is_closed_sat: false,
    day_of_week_sun: '',
    opening_time_sun: '',
    closing_time_sun: '',
    is_closed_sun: false,

    // 画像のアップロード
    main_image_url: '',
    image1_url: '',
    image2_url: '',
    image3_url: '',
    image4_url: '',
    image5_url: '',
});

// 選択肢として用意
const { facilityTypes, usageTypes, prefectures, saunaTypes, stoveTypes, heatTypes, waterTypes, bathTypes } = usePage().props;

// 施設タイプ
const facilityTypesArray = Array.isArray(facilityTypes) ? facilityTypes : [facilityTypes];
const selectedFacilityType = ref(null);
// 利用形態
const usageTypeArray = Array.isArray(usageTypes) ? usageTypes : [usageTypes];
const selectedUsageType = ref(null);
// 都道府県
const prefectureArray = Array.isArray(prefectures) ? prefectures : [prefectures];
const selectedPrefecture = ref(null);
// サウナタイプ
const saunaTypeArray = Array.isArray(saunaTypes) ? saunaTypes : [saunaTypes];
const selectedSaunaType = ref(null);
// ストーブタイプ(radio)
const stoveTypeArray = Array.isArray(stoveTypes) ? stoveTypes : [stoveTypes];
const stoveTypeName = stoveTypes.map(type => type.type_name);
const selectedStoveType = ref(null)
// 熱源(radio)
const heatTypeArray = Array.isArray(heatTypes) ? heatTypes : [heatTypes];
const heatTypeName = heatTypes.map(type => type.source_name);
// 水タイプ
const waterTypeArray = Array.isArray(waterTypes) ? waterTypes : [waterTypes];
const selectedWaterType = ref(null)
// 水風呂タイプ
const bathTypeArray = Array.isArray(bathTypes) ? bathTypes : [bathTypes];
const selectedBathType = ref(null)

// waterOptions を定義(DBで定義ではない) todo refでラップする必要なし
const waterDepthOptions = ref([
    { id: 1, type_name: '20 ~ 40cm(すね)' },
    { id: 2, type_name: '40 ~ 60cm(ひざ)' },
    { id: 3, type_name: '60 ~ 80cm(股下)' },
    { id: 4, type_name: '80 ~ 110cm(腰)' },
    { id: 5, type_name: '110 ~ 140cm(胸)' },
    { id: 6, type_name: '140cm ~ (肩)' }
]);

// 営業時間の曜日定義 todo refでラップする必要なし
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

// フォームを送信した時に実行(preventされるのでinputタグは手でformの中身を更新する必要あり)
const updateSaunaInformation = () => {
    // input タグの内容をフォームデータに追加(day_of_week_monに月など)
    Object.keys(daysOfWeek.value).forEach(day => {
      const dayAbbreviation = daysOfWeek.value[day];
      form[`day_of_week_${dayAbbreviation}`] = day;
    });

    // フォームの内容をSaunaControllerのstoreアクションへ送信
    Inertia.post(route('sauna.store'), form, {
        // errorBag: 'updateProfileInformation',
        // preserveScroll: true,
        // onSuccess: () => clearPhotoFileInput(),
    });
};

// マウント時にv-forしたinput要素(file、hidden)をimageInputsに追加
onMounted(() => {
  const fileInputs = document.querySelectorAll('input[type="file"].hidden');
  fileInputs.forEach((input, index) => {
    // input要素をimageInputsに追加
    imageInputs.value.push(input);
  });
});

// 画像選択ボタンを押した時に、input要素を取得してクリック
const selectNewPhoto = (index) => {
  // 対応する input 要素を取得し、クリックイベントをトリガーする
  const input = imageInputs.value[index];
  if (input) {
    input.click();
  }
};

// 画像を選択したときに実行(for対応)→バリデーションも実行
const selectImage = (event, field, index) => {
  const file = event.target.files[0];

  // サイズのバリデーション
  let validationResult = isValidImageSize(file);
  if (!validationResult.isValid) {
    form.errors[field] = validationResult.errorMessage;
    return;
  }

  // 形式のバリデーション
  validationResult = isValidImageType(file);
  if (!validationResult.isValid) {
    form.errors[field] = validationResult.errorMessage;
    return;
  }

  form.errors[field] = ""; // エラーをクリア

  const reader = new FileReader();
  reader.onload = (e) => {
    // formデータに画像のURLを設定
    form[field] = file;
    // プレビュー画像表示のためにimageUrlsのvalueを更新
    imageUrls.value[index].value = reader.result;
  };
  reader.readAsDataURL(file);
};

// 画像を削除するときに実行(for対応)
const deleteImage = (field, index) => {
    form[field] = '';
    // プレビュー画像表示のためにimageUrlsのvalueを更新
    imageUrls.value[index].value = '';
};

// バリデーション
const validText = (field, max, min) => {
  const {isValid, errorMessage } = isValidText(form[field], max, min);
  if(!isValid) {
    form.errors[field] = errorMessage;
  } else {
    form.errors[field] = "";
  }
}

const validMax = (field, max) => {
  const {isValid, errorMessage} = isValidMax(form[field], max);
  if(!isValid) {
    form.errors[field] = errorMessage;
  } else {
    form.errors[field] = "";
  }
}

const validPhone = () => {
  const { isValid, errorMessage } = isValidPhone(form.tel);
  if(!isValid && form.tel !== "") {
    form.errors.tel = errorMessage;
  } else {
    form.errors.tel = "";
  }
}

const validUrl = () => {
  const { isValid, errorMessage} = isValidUrl(form.website_url);
  if(!isValid) {
    form.errors.website_url = errorMessage;
  } else {
    form.errors.website_url = "";
  }
}

const validNumber = (field) => {
  const { isValid, errorMessage } = isValidNumber(form[field]);
  if(!isValid) {
    form.errors[field] = errorMessage;
  } else {
    form.errors[field] = "";
  }
}
</script>

<template>
    <div class="mb-16 px-4 py-5 sm:px-20 sm:py-10 bg-sub shadow rounded-lg text-white">
      <h2 class="text-2xl font-bold text-white text-center mb-10 ">サウナ施設の新規登録</h2>
      <p>会員登録されている方であれば、誰でも施設情報を登録・更新することができます。</p>
      <p>皆様のサウナライフをより良いものにしていただくために、より正確な情報をご提供いただけると幸いです。</p>
      <p>すでに施設情報が登録されており、重複登録となってしまった場合は運営側で削除させていただくこともございますので、</p>
      <p>登録前に一度施設名で検索していただき、登録がないことを確認していただけると幸いです。</p>
    </div>

     <!-- 施設情報入力 -->
    <FormSection @submitted="updateSaunaInformation">
        <template #title>
        </template>

        <template #description>
            ※わからない場合は未記入のまま進めていただいて構いません
        </template>

        <template #form>
            <!-- 項目数合計24個 施設情報(12項目)　サウナ情報(6項目)　水風呂情報(6項目)　画像 -->
            <h3 class="col-span-6 text-xl font-bold">【施設情報】</h3>
            <!-- 施設名 -->
            <div class="col-span-6 sm:col-span-6">
                <InputLabel for="facility_name" value="施設名 (※必須)" />
                <TextInput
                    id="facility_name"
                    v-model="form.facility_name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="facility_name"
                    placeholder="例： サウナ＆スパ カプセルホテル 大東洋"
                    @blur="() => validText('facility_name', 50, 0)"
                />
                <InputError :message="form.errors.facility_name" class="mt-2" />
            </div>

            <!-- 施設タイプ -->
            <div class="col-span-6 sm:col-span-3">
                <SelectBox 
                  id="facility_type" 
                  label="施設タイプ (※必須)" 
                  v-model="form.facility_type_id"
                  :initialValue="selectedFacilityType" 
                  :options="facilityTypesArray" 
                  required
                />
                <InputError :message="form.errors.facility_type_id" class="mt-2" />
            </div>

            <!-- 利用形態 -->
            <div class="col-span-6 sm:col-span-3">
                <SelectBox 
                  id="usage_type" 
                  label="利用形態 (※必須)" 
                  v-model="form.usage_type_id"
                  :initialValue="selectedUsageType" 
                  :options="usageTypeArray" 
                  required
                />
                <InputError :message="form.errors.usage_type_id" class="mt-2" />
            </div>

            <!-- 都道府県 -->
            <div class="col-span-3 sm:col-span-2">
                <SelectBox 
                  id="prefecture_id" 
                  label="都道府県 (※必須)" 
                  column="name"
                  v-model="form.prefecture_id"
                  :initialValue="selectedPrefecture" 
                  :options="prefectureArray" 
                  required
                />
                <InputError :message="form.errors.prefecture_id" class="mt-2" />
            </div>

            <!-- 住所1 -->
            <div class="col-span-6">
                <InputLabel for="address1" value="住所1 (※必須)" />
                <TextInput
                    id="address1"
                    v-model="form.address1"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="address1"
                    placeholder="例： 大阪市北区中崎西"
                    @blur="() => validMax('address1', 50)"
                />
                <InputError :message="form.errors.address1" class="mt-2" />
            </div>
            <!-- 住所2 -->
            <div class="col-span-6">
                <InputLabel for="address2" value="住所2 (※必須)" />
                <TextInput
                    id="address2"
                    v-model="form.address2"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="address2"
                    placeholder="例： 2-1-9"
                    @blur="() => validMax('address2', 50)"
                />
                <InputError :message="form.errors.address2" class="mt-2" />
            </div>
            <!-- 住所3 -->
            <div class="col-span-6">
                <InputLabel for="address3" value="住所3" />
                <TextInput
                    id="address3"
                    v-model="form. address3"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="address3"
                    placeholder="例： 中崎観光ビル大東洋"
                    @blur="() => validMax('address3', 50)"
                />
                <InputError :message="form.errors.address3" class="mt-2" />
            </div>

            <!-- アクセス詳細 -->
            <div class="col-span-6">
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
                    @blur="() => validMax('access_text', 1000)"
                />
                <InputError :message="form.errors.access_text" class="mt-2" />
            </div>

            <!-- 電話番号 -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="tel" value="電話番号" />
                <TextInput
                    id="tel"
                    v-model="form.tel"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="tel"
                    placeholder="例： 06-6312-7522"
                    @blur="validPhone"
                />
                <InputError :message="form.errors.tel" class="mt-2" />
            </div>

            <!-- WebURL -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="website_url" value="Webサイト" />
                <TextInput
                    id="website_url"
                    v-model="form.website_url"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="website_url"
                    placeholder="例： http://www.daitoyo.co.jp/spa/mens/"
                    @blur="validUrl"
                />
                <InputError :message="form.errors.website_url" class="mt-2" />
            </div>

            <!-- 営業時間 -->
            <div class="col-span-6 pb-6 border-gray-300 border-2 rounded-xl">
              <div class="grid grid-cols-7">
                <div v-for="(alfDay, day) in daysOfWeek" 
                  :key="day" 
                  @click="selectDay(alfDay)" 
                  class="inline-block p-4 mb-6 text-center text-gray-600 font-bold border border-gray-400 rounded"
                  :class="[selectedDay === alfDay ? ['bg-gray-500', 'text-white'] : 'bg-gray-200']">
                  {{ day }}
                </div>
              </div>

              <div class="grid grid-cols-6 gap-6 ml-8">
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
                      class="m-4 form-checkbox h-8 w-8 text-gray-600 rounded-lg border-none focus:ring-0 shadow-none checked:bg-gray-600 bg-gray-200"
                    >
                    <label class="inline-block text-center text-l">定休日</label>
                  </div>

                  <div v-if="!form['is_closed_' + alfDay]">
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
                      <span class="flex justify-center pt-4 items-center h-full">〜</span>
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
                  <div v-else>
                    <div class="hidden">
                        <!-- 開始時間の入力フィールド -->
                        <InputLabel for="'opening_time_' + alfDay" value="営業開始時間" />
                          <TextInput
                              id="'opening_time_' + alfDay"
                              value=""
                              type="time"
                          />
                      </div>
                      <div class="hidden">
                        <!-- 終了時間の入力フィールド -->
                        <InputLabel for="'closing_time_' + alfDay" value="営業終了時間" />
                          <TextInput
                              id="'closing_time_' + alfDay"
                              value=""
                              type="time"
                          />
                      </div>
                  </div>

                </div>
              </div>
            </div>

            <!-- 営業時間詳細 -->
            <div class="col-span-6">
                <InputLabel for="business_hours_detail" value="営業時間詳細" />
                <Textarea
                    id="business_hours_detail"
                    v-model="form.business_hours_detail"
                    type="text"
                    class="mt-1 block w-full min-h-48"
                    autocomplete="business_hours_detail"
                    placeholder="例： 毎日営業しているが、午前10時から午後12時までは清掃のため入浴不可"
                    @blur="validMax('business_hours_detail', 1000)"
                />
                <InputError :message="form.errors.business_hours_detail" class="mt-2" />
            </div>

            <!-- 最低料金 todo 数字のみの入力で自動で円入力 -->
            <div class="col-span-3 sm:col-span-2">
                <InputLabel for="min_fee" value="最低料金" />
                <div class="relative">
                  <TextInput
                    id="min_fee"
                    v-model="form.min_fee"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="min_fee"
                    placeholder="例： 300"
                    @blur="() => validNumber('min_fee')"
                  />
                  <span class="absolute inset-y-0 right-4 pr-3 flex items-center">円</span>
                </div>
                <InputError :message="form.errors.min_fee" class="mt-2" />
            </div>
            
            <!-- 料金詳細 -->
            <div class="col-span-6">
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
                    @blur="validMax('fee_text', 1000)"
                />
                <InputError :message="form.errors.fee_text" class="mt-2" />
            </div>


            <div class="col-span-6 py-8">
              <div class="border-t border-gray-400" /> 
            </div>
            <h3 class="col-span-6 text-xl font-bold">【サウナ情報】</h3>
            <!-- todo サウナ室が追加できるような仕様 -->
            <!-- サウナタイプ -->
            <div class="col-span-6 sm:col-span-2">
                <SelectBox 
                  id="sauna_type_id" 
                  label="サウナタイプ" 
                  v-model="form.sauna_type_id"
                  :initialValue="selectedSaunaType" 
                  :options="saunaTypeArray" 
                />
            </div>

            <!--  ストーブタイプ -->
            <div class="col-span-6">
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

            <!-- 熱源 -->
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

            <!-- 温度 -->
            <div class="col-span-6">
              <div class="grid grid-cols-6">
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
                        @blur="() => validNumber('temperature_sauna')"
                    />
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center">度</span>
                  </div>
                  <InputError :message="form.errors.temperature_sauna" class="mt-2" />
                </div>
              </div>
            </div>

            <!-- 収容人数 -->
            <div class="col-span-6">
              <div class="grid grid-cols-6">
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
                        @blur="() => validNumber('capacity_sauna')"
                    />
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center">人</span>
                  </div>
                  <InputError :message="form.errors.capacity_sauna" class="mt-2" />
                </div>
              </div>
            </div>

            <!-- サウナ詳細 -->
            <div class="col-span-6">
                <InputLabel for="additional_info" value="補足" />
                <Textarea
                    id="additional_info"
                    v-model="form.additional_info_sauna"
                    type="text"
                    class="mt-1 block w-full min-h-32"
                    autocomplete="additional_info"
                    placeholder="例： タオルに水を染み込ませて持ち込まないローカルなルール"
                    @blur="() => validMax('additional_info_sauna', 1000)"
                />
                <InputError :message="form.errors.additional_info_sauna" class="mt-2" />
            </div>


            <div class="col-span-6 py-8">
              <div class="border-t border-gray-400" /> 
            </div>
            <h3 class="col-span-6 text-xl font-bold">【水風呂情報】</h3>
            <!-- todo 水風呂が追加できるような仕様 -->
            <!-- 水風呂タイプ -->
            <div class="col-span-6 sm:col-span-3">
                <SelectBox 
                  id="bath_type_id" 
                  label="タイプ" 
                  v-model="form.bath_type_id"
                  :initialValue="selectedBathType" 
                  :options="bathTypeArray" 
                />
            </div>

            <!-- 水の種類 -->
            <div class="col-span-6 sm:col-span-3">
                <SelectBox 
                  id="water_type_id" 
                  label="水" 
                  v-model="form.water_type_id"
                  :initialValue="selectedWaterType" 
                  :options="waterTypeArray" 
                />
            </div>

            <!-- 水風呂温度 -->
            <div class="col-span-6">
              <div class="grid grid-cols-6">
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
                        @blur="() => validNumber('temperature_water')"
                    />
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center">度</span>
                  </div>
                  <InputError :message="form.errors.temperature_water" class="mt-2" />
                </div>
              </div>
            </div>

            <!-- 水風呂収容人数 -->
            <div class="col-span-6">
              <div class="grid grid-cols-6">
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
                        @blur="() => validNumber('capacity_water')"
                    />
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center">人</span>
                  </div>
                  <InputError :message="form.errors.capacity_water" class="mt-2" />
                </div>
              </div>
            </div>

            <!-- 水深 -->
            <div class="col-span-6 sm:col-span-3">
                <SelectBox 
                  id="deep_water" 
                  label="水深"
                  v-model="form.deep_water"
                  :initialValue="selectedSaunaType" 
                  :options="waterDepthOptions" 
                />
            </div>

            <!-- 水風呂補足 -->
            <div class="col-span-6">
                <InputLabel for="additional_info" value="補足" />
                <Textarea
                    id="additional_info"
                    v-model="form.additional_info_water"
                    type="text"
                    class="mt-1 block w-full min-h-32"
                    autocomplete="additional_info"
                    placeholder="例： 水風呂ではなく掛け流しタイプ"
                    @blur="() => validMax('additional_info_sauna', 1000)"
                />
                <InputError :message="form.errors.access_text_water" class="mt-2" />
            </div>



            <div class="col-span-6 py-8">
              <div class="border-t border-gray-400" /> 
            </div>
            <h3 class="col-span-6 text-xl">【画像登録】</h3>
            <span class="col-span-4">※1枚目のメイン画像(外観)は必須</span>
          <!-- 画像アップロード 6枚(main1枚と1~5のimage) -->
          <div class="col-span-6">
                <!-- 施設メイン画像のinput -->
                <div class="grid grid-cols-6 gap-8">
                  <!-- todo 各画像のキャプションを追加 -->
                    <div v-for="(image, index) in imageUrls" :key="index" class="col-span-3 md:col-span-2 sm:col-span-3">
                      <input
                        :ref="`imageInputs${index}`"
                        type="file"
                        class="hidden"
                        @change="selectImage($event, image.key, index)"
                      >
                      <InputLabel :for="`imageInput${image.key}`" value="関連画像" />

                      <!-- 初期施設写真 -->
                      <div v-show="! image.value" class="mt-2 w-full" @click="selectNewPhoto(index)">
                          <img 
                            :src="`${baseUrl}storage/default-images/no_image.jpg`"
                            :alt="sauna.facility_name"
                            class="rounded w-full object-cover">
                      </div>

                      <!-- 挿入画像のプレビュー -->
                      <div v-show="image.value" class="mt-2" @click="selectNewPhoto(index)">
                          <span
                              class="block rounded w-full bg-cover bg-no-repeat bg-center"
                              :style="{ backgroundImage: `url(${image.value})`,
                              paddingBottom: '100%' }"
                          />
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
                      <InputError :message="form.errors[image.key]" class="mt-2" />
                    </div>
                </div>
            </div>
        </template>

        <!-- 保存ボタン -->
        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                登録完了
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" class="bg-sub" :disabled="form.processing">
                登録する
            </PrimaryButton>
        </template>
    </FormSection>
</template>
