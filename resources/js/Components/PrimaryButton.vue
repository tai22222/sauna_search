<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type"
            class="inline-flex items-center px-12 md:px-4 py-3 md:py-2 bg-white border-2 border-gray-500 rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest 
            hover:bg-gray-300/70 hover:text-white focus:bg-gray-500 focus:text-white active:bg-gray-500/70 active:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
        <slot />
    </button>
</template>
