<script setup>
import { ref, computed, defineProps} from 'vue';
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

// バリデーション
import { isValidText, isValidEmail, isValidMax } from '@/utils/validators';

// 親コンポーネント(Create.vue)からオブジェクト、配列の受け渡し
const props = defineProps({
    user: Object,
});

// フォームで受け取るデータ
const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null,
    gender_id: props.user.gender_id,
    birth_year: props.user.birth_year,
    birth_month: props.user.birth_month,
    birth_day: props.user.birth_day,
    debut_year: props.user.debut_year,
    debut_month: props.user.debut_month,
    home_sauna: props.user.home_sauna,
    profile_text: props.user.profile_text,
});

// setup ブロック内で genderOptions を定義
const genderOptions = ref([
    { value: 1, label: '男性' },
    { value: 2, label: '女性' },
]);

// 西暦の定義（オブジェクト形式）
const years = ref([]);
let yearId = 1; // 年のIDを1から始める
for (let year = new Date().getFullYear(); year >= 1900; year--) {
  years.value.push({ id: yearId++, value: year });
}

// 月の定義(1~12月)
const months = ref([...Array(12).keys()].map((month, index) => ({ id: index + 2, value: month + 1 })));

// 日の定義(1~31日)
const daysInMonth = computed(() => {
  if (form.birth_month) {
    const days = new Array(31).fill().map((_, index) => ({ id: index + 2, value: index + 1 }));
    return days;
  }
  return [];
});

// 生年月日の更新
const updateYear = (type, value) => {
  const prop = type === 'birth' ? 'birth_year' : 'debut_year';
  form[prop] = value;
};

const updateMonth = (type, value) => {
  const prop = type === 'birth' ? 'birth_month' : 'debut_month';
  form[prop] = value;
};

const updateDay = (type, value) => {
  const prop = type === 'birth' ? 'birth_day' : 'debut_day';
  form[prop] = value;
};

// フォーム送信時の画像データをformに詰めて、formをpostする
const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const verificationLinkSent = ref(null);

// Eメール変更時
const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const photoPreview = ref(null);
const photoInput = ref(null);

// inputをクリックした挙動を発生
const selectNewPhoto = () => {
    photoInput.value.click();
};

// 選択した画像をプレビュー画像として使用できるようにする
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

// バリデーション
const validText = () => {
  const { isValid, errorMessage } = isValidText(form.name);
  if(!isValid){
    form.errors.name = errorMessage;
  } else {
    form.errors.name = "";
  }
}

const validEmail = () => {
  const { isValid, errorMessage } = isValidEmail(form.email);
  if(!isValid) {
    form.errors.email = errorMessage;
  } else {
    form.errors.email = "";
  }
}

const validMax = (field, max) => {
  const {isValid, errorMessage } = isValidMax(form[field], max);
  if(!isValid) {
    form.errors[field] = errorMessage;
  } else {
    form.errors[field] = "";
  }
}
</script>

<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            プロフィール
        </template>

        <template #description>
            アカウントのプロフィール情報とメールアドレスを更新してください。
        </template>

        <template #form>
            <!-- プロフィール写真 -->
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6">
                <!-- プロフィール写真のinput -->
                <input
                    ref="photoInput"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                >
                <InputLabel for="photo" value="アイコン画像" />

                <!-- 現在のプロフィール写真 -->
                <div v-show="! photoPreview" class="mt-2">
                    <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- 新しいのプロフィール写真のプレビュー -->
                <div v-show="photoPreview" class="mt-2">
                    <span
                        class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                        :style="'background-image: url(\'' + photoPreview + '\');'"
                    />
                </div>

                <!-- プロフィール画像追加・削除ボタン -->
                <SecondaryButton class="mt-2 mr-2"
                                 type="button"
                                 @click.prevent="selectNewPhoto">
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
            </div>

            <!-- 名前 -->
            <div class="col-span-6 sm:col-span-2">
                <InputLabel for="name" value="ユーザー名" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="name"
                    @blur="validText"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Email-->
            <div class="col-span-6 sm:col-span-2">
                <InputLabel for="email" value="メールアドレス" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    autocomplete="username"
                    @blur="validEmail"
                />
                <InputError :message="form.errors.email" class="mt-2" />

                <!-- メール認証機能 -->
                <!-- todo 追加できるように -->
                <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <p class="text-sm mt-2">
                        Your email address is unverified.

                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            @click.prevent="sendEmailVerification"
                        >
                            Click here to re-send the verification email.
                        </Link>
                    </p>

                    <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-600">
                        A new verification link has been sent to your email address.
                    </div>
                </div>
            </div>

            <!-- 性別 -->
            <div class="col-span-6">
              <InputLabel for="gender_id" value="性別" />
              <div class="mt-1">
                <label v-for="(option, index) in genderOptions" :key="index" class="inline-flex items-center ml-2">
                <input
                  type="radio"
                  class="form-radio"
                  :name="option.label"
                  :value="option.value"
                  v-model="form.gender_id"
                />
                <span class="ml-2">{{ option.label }}</span>
                </label>
              </div>
            <InputError :message="form.errors.gender_id" class="mt-2" />
            </div>

            <!-- 生年月日 -->
            <div class="col-span-6 sm:col-span-6">
              <div class="mb-2">
                <p class="text-sm font-medium text-gray-700">生年月日</p>
              </div>
              <div class="flex space-x-2">
                <SelectBox 
                  id="birth_year" 
                  label="年" 
                  column="value"
                  :initialValue="form.birth_year.toString()" 
                  :options="years" 
                  :error="form.errors.birth_year" 
                  @update:modelValue="updateYear('birth', $event)"
                />
                <SelectBox 
                  id="birth_month" 
                  label="月" 
                  column="value"
                  :initialValue="form.birth_month.toString()" 
                  :options="months" 
                  :error="form.errors.birth_month" 
                  @update:modelValue="updateMonth('birth', $event)"
                />
                <SelectBox 
                  id="birth_day" 
                  label="日" 
                  column="value"
                  :initialValue="form.birth_day.toString()" 
                  :options="daysInMonth" 
                  :error="form.errors.birth_day" 
                  @update:modelValue="updateDay('birth', $event)"
                />
              </div>
            </div>

            <!-- サウナ デビュー年月 -->
            <div class="col-span-6 sm:col-span-6">
              <div class="mb-2">
                <p class="text-sm font-medium text-gray-700">サウナデビュー年月</p>
              </div>
              <div class="flex space-x-2">
                <SelectBox 
                  id="debut_year" 
                  label="年" 
                  column="value"
                  :initialValue="form.debut_year.toString()" 
                  :options="years" 
                  :error="form.errors.debut_year" 
                  @update:modelValue="updateYear('debut', $event)"
                />

                <SelectBox 
                  id="debut_month" 
                  label="月" 
                  column="value"
                  :initialValue="form.debut_month.toString()" 
                  :options="months" 
                  :error="form.errors.debut_month" 
                  @update:modelValue="updateMonth('debut', $event)"
                />
              </div>
            </div>

            <!-- home_sauna -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="home_sauna" value="ホームサウナ" />
                <TextInput
                    id="home_sauna"
                    v-model="form.home_sauna"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="home_sauna"
                    @blur="() => validMax('home_sauna', 50)"
                />
                <InputError :message="form.errors.home_sauna" class="mt-2" />
            </div>

            <!-- profile_text -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="profile_text" value="自己紹介" />
                <Textarea
                    id="profile_text"
                    v-model="form.profile_text"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="profile_text"
                    @blur="() => validMax('profile_text', 250)"
                />
                <InputError :message="form.errors.profile_text" class="mt-2" />
            </div>

        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                保存完了
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                保存
            </PrimaryButton>
        </template>
    </FormSection>
</template>
