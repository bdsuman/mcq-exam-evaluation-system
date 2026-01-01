import { ref } from 'vue';

const successRef = ref(null);
const failRef = ref(null);

const notify = {
  success(options) {
    successRef.value?.open(options);
  },
  error(options) {
    failRef.value?.open(options);
  },
};

export function useNotificationRefs() {
  return { successRef, failRef };
}

export function useNotify() {
  return notify;
}
