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
import SectionBorder from '@/Components/SectionBorder.vue';

// 親コンポーネント(Create.vue)からオブジェクト、配列の受け渡し
const props = defineProps({
    user: Object,
    reviews: Object,
    favorites: Object,
    reviewsCount: Number,
    favoritesCount: Number,
});
console.log(props);

const {user, reviews, favorites} = usePage().props;

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

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

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

// Eメール変更時
const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

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

console.log(process.env.NODE_ENV);
</script>

<template>
  <div class="grid grid-cols-12 gap-4 bg-white p-12 mb-8 rounded-lg shadow">
    <div class="col-span-12">
      <p>登録日:{{ user.created_at }}</p>
    </div>
    <div class="col-span-2">
      <span
        class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
        :style="'background-image: url(\'' + user.profile_photo_url + '\');'"
      />
    </div>
    <div class="col-span-4">
      {{ user.name }}
    </div>
    <div class="col-span-12 grid grid-cols-12 gap-4">
      <div class="col-span-2">
        ホームサウナ
      </div>
      <div class="col-span-10">
        {{ user.home_sauna }}
      </div>
      <div class="col-span-2">
        プロフィール
      </div>
      <div class="col-span-10">
        {{ user.profile_text }}
      </div>
    </div>

    <div class="col-span-12">
      <SectionBorder />
    </div>
    
    <div class="col-span-3">
      Myレビュー( {{ $page.props.reviewsCount }} 件)
    </div>
    <div class="col-span-3">
      お気に入りサウナ( {{ $page.props.favoritesCount }} 件)
    </div>
  </div>

  <div class="grid grid-cols-6 gap-6">
    <div class="col-span-2 bg-white p-12 rounded-lg shadow">
      検索
    </div>
    
    <div class="col-span-4 bg-white p-12">
      <div class="mb-2">
        サ活一覧(usersに紐づいたreviews)
      </div>

      <div  v-for="review in reviews"
            :key="review"
            class="grid grid-cols-6 gap-6 bg-gray-200 p-4 mb-4 rounded-2xl shadow">
        <div class="col-span-1">
          <span
            class="block rounded-full w-12 h-12 bg-cover bg-no-repeat bg-center"
            :style="'background-image: url(\'' + user.profile_photo_url + '\');'"
          />
        </div>
        <div class="col-span-5">
          ユーザー名: {{ user.name }}<br>
          訪問日:{{ review.visited_date }}
        </div>
        <div class="col-span-6">
          {{ review.sauna.facility_name }}  [{{ review.sauna.prefecture.name }}]
        </div>
        <div class="col-span-6">
          タイトル {{ review.title }}
        </div>
        <div class="col-span-6">
          文章 {{ review.content }}
        </div>
      </div>

    </div>
  </div>
</template>
