// middleware/auth.js
export default defineNuxtRouteMiddleware((to, from) => {
    const jwtCookie = useCookie('jwt'); // استفاده از useCookie برای خواندن کوکی

    // اگر توکن JWT وجود نداشته باشد و کاربر در صفحه ورود (/login) نباشد، به صفحه ورود هدایت می‌شود
    if (!jwtCookie.value && to.path !== '/login') {
        return navigateTo('/login'); // هدایت به صفحه ورود
    }

    // اگر توکن JWT وجود داشته باشد و کاربر در صفحه ورود (/login) باشد، به صفحه اصلی هدایت می‌شود
    if (jwtCookie.value && to.path === '/login') {
        return navigateTo('/'); // هدایت به صفحه اصلی یا هر صفحه دیگری که مناسب باشد
    }
});
