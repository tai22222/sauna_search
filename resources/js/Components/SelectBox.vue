<script setup>
import { onMounted, ref, defineProps, defineEmits, defineExpose } from 'vue';
import InputError from '@/Components/InputError.vue';

// initialValueは初期値、modeValueは入力値、optionsは選択肢としての配列、columnはDBのカラム名
const props = defineProps({
    modelValue: { String, Number},
    initialValue: { String, Number},
    id: String,
    label: String,
    options: Array,
    typesArray: Array,
    column: {
      type: String,
      default: 'type_name'
  }
});

const input = ref(null);
// 選択されている値を取得
const selectedValue = ref(props.modelValue);
// 親コンポーネントに受け渡す
const emit = defineEmits(['update:modelValue']);


// フォームの初期値を設定(マウント時に設定)
onMounted(() => {
  selectedValue.value = props.initialValue;
});

// optionの内容が変更されたら検知し、selectedValueを更新(親コンポーネントへ渡す値)
function handleChange(event) {
  const newValue = event.target.value;
  selectedValue.value = parseInt(newValue);
  emit('update:modelValue', newValue);
}

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
  <div class="col-span-6 sm:col-span-2">
    <div class="mb-2">
      <p class="text-sm font-medium text-gray-700">{{ props.label }}</p>
    </div>
    <select 
      :id="props.id" 
      v-model="selectedValue"
      class="mt-1 block w-full border-gray-300 border-2 focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2 rounded-xl shadow-sm" 
      ref="selectBox" 
      @change="handleChange" 
    >
      <option :value="null">選択してください</option>
      <option 
        v-for="option in props.options" 
        :key="option.id" 
        :value="option.id">{{ option[props.column]}}</option>
    </select>
  </div>
</template>
