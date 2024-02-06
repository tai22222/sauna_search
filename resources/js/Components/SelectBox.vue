<script setup>
import { onMounted, ref, defineProps, defineEmits, defineExpose } from 'vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    modelValue: String,
    id: String,
    initialValue: String,
    label: String,
    options: Array,
    error: String,
});


// defineEmits(['update:modelValue']);
const emit = defineEmits(['update:modelValue']);

// optionの内容が変更されたら検知し、modelValueを更新
function handleChange(event) {
  // 選択された値を取得
  const selectedValue = event.target.value;
  // 親コンポーネントに選択された値を渡す
  emit('update:modelValue', selectedValue);
}


const input = ref(null);
const selectedValue = ref(null);

// フォームの初期値を設定
onMounted(() => {
  selectedValue.value = props.initialValue;
});

// onMounted(() => {
//     // if (input.value.hasAttribute('autofocus')) {
//     //     input.value.focus();
//     // }
//     input.value.value = props.modelValue;
// });

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
  <div class="col-span-6 sm:col-span-2">
    <div class="mb-2">
      <p class="text-sm font-medium text-gray-700">{{ props.label }}</p>
      <p>{{ props.modelValue }}</p>
      
    </div>
    <select 
      :id="id" 
      v-model="selectedValue"
      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
      ref="selectBox" 
      @change="handleChange" 
    >
      <option :value="0">選択してください</option>
      <option v-for="option in props.options" :key="option" :value="option">{{ option }} </option>
    </select>
    <InputError :message="error" class="mt-2" />
  </div>
  <!-- <p>{{ props.options }}</p> -->
</template>
