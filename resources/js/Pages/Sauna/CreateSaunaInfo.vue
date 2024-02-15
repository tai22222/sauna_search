<script setup>
import { ref, computed, defineProps } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';

import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectBox from '@/Components/SelectBox.vue';

// 親コンポーネント(Create.vue)からオブジェクト、配列の受け渡し(CompositionAPI、ObjectはArrayも含む)
const props = defineProps({
    user: Object,
    sauna: Object,
    facilityType: Object,
    usageType: Object,
    prefecture: Object,
    saunaInfo: Object,
    saunaType: Object,
    stoveType: Object,
    heatType: Object,
    waterBath: Object,
    bathType: Object,
    waterType: Object,
});

// フォームの各入力項目に対応するPOSTデータ(saunasテーブル、saunas_infosテーブル、water_bathsテーブル)
const form = useForm({
    _method: 'POST',
    user_id: props.user.id,

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

    sauna_type_id:'',
    stove_type_id:'',
    heat_type_id:'',
    temperature_sauna:'',
    capacity_sauna:'',
    additional_info_sauna:'',

    bath_type_id:'',
    water_type_id:'',
    temperature_water:'',
    capacity_water:'',
    deep_water:'',
    additional_info_water:'',
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

// setup ブロック内で waterOptions を定義(DBで定義ではない)
const waterDepthOptions = ref([
    { id: 1, type_name: '20 ~ 40cm(すね)' },
    { id: 2, type_name: '40 ~ 60cm(ひざ)' },
    { id: 3, type_name: '60 ~ 80cm(股下)' },
    { id: 4, type_name: '80 ~ 110cm(腰)' },
    { id: 5, type_name: '110 ~ 140cm(胸)' },
    { id: 6, type_name: '140cm ~ (肩)' }
]);

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('sauna.store'), {
        // errorBag: 'updateProfileInformation',
        // preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

// const sendEmailVerification = () => {
//     verificationLinkSent.value = true;
// };

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (! photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
console.log(props);
</script>

<template>
    <div class="mb-16 px-4 py-5 bg-white sm:p-20 shadow">
      <h2 class="text-2xl font-bold text-gray-900 text-center mb-10 ">施設を登録</h2>
      <p>会員登録されている方であれば、誰でも施設情報を登録・更新することができます。</p>
      <p>皆様のサウナライフをより良いものにしていただくために、より正確な情報をご提供いただけると幸いです。</p>
      <p>すでに施設情報が登録されており、重複登録となってしまった場合は運営側で削除させていただくこともございますので、</p>
      <p>登録前に一度施設名で検索していただき、登録がないことを確認していただけると幸いです。</p>
    </div>

     <!-- 施設情報入力 -->
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            【施設情報入力】
        </template>

        <template #description>
            ※わからない場合は未記入のまま進めていただいて構いません
        </template>

        <template #form>
            <!-- プロフィール写真 -->
            <!-- <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4"> -->
                <!-- プロフィール写真のinput -->
                <!-- <input
                    ref="photoInput"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                >

                <InputLabel for="photo" value="アイコン画像" /> -->

                <!-- 現在のプロフィール写真 -->
                <!-- <div v-show="! photoPreview" class="mt-2">
                    <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover">

                </div> -->

                <!-- 新しいのプロフィール写真のプレビュー -->
                <!-- <div v-show="photoPreview" class="mt-2">
                    <span
                        class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                        :style="'background-image: url(\'' + photoPreview + '\');'"
                    />
                </div> -->

                <!-- プロフィール画像追加・削除ボタン -->
                <!-- <SecondaryButton class="mt-2 mr-2" type="button" @click.prevent="selectNewPhoto">
                    画像を選択
                </SecondaryButton>

                <SecondaryButton
                    v-if="user.profile_photo_path"
                    type="button"
                    class="mt-2"
                    @click.prevent="deletePhoto"
                >
                    削除
                </SecondaryButton>

                <InputError :message="form.errors.photo" class="mt-2" />
            </div> -->

            <!-- 項目数 施設情報(12項目)　サウナ情報(6項目)　水風呂情報(6項目)　画像 -->
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

            <!-- Saunas.Genders Gender input 不要 -->
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

            <!-- Business_hours.Saunas business_hours hours 曜日ごとに(foreach) -->
              <!-- Business_hours day_of_week integer -->
              <!-- Business_hours opening_time -->
              <!-- Business_hours closing_time -->
              <!-- Business_hours is_closed boolean -->

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

            <!-- Images_facilities.Saunas main_image_url input -->
            <!-- Images_facilities.Saunas image1_url input -->
            <!-- Images_facilities.Saunas image2_url input -->
            <!-- Images_facilities.Saunas image3_url input -->
            <!-- Images_facilities.Saunas image4_url input -->
            <!-- Images_facilities.Saunas image5_url input -->


        </template>

        <!-- 保存ボタン -->
        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
