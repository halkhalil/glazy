<template>
    <div class="container-fluid app-container">
        <AppHeader/>
        <transition
            name="fade"
            mode="out-in"
        >
            <router-view/>
        </transition>
    </div>
</template>

<script>
import AppHeader from "./AppHeader.vue";

export default {
  name: "AppLayoutFull",
  components: {
    AppHeader
  },
  mounted() {}
}
</script>

<style>
.app-container {
}

.fade-enter-active,
.fade-leave-active {
  transition-duration: 0.3s;
  transition-property: opacity;
  transition-timing-function: ease;
}

.fade-enter,
.fade-leave-active {
  opacity: 0;
}
</style>