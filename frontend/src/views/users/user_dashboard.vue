<template>
  <UserLayout
    title="Dashboard"
    subtitle="Overview of your account and rewards"
    active="dashboard"
    v-model:sidebarOpen="sidebarOpen"
  >
    <template #default="{ me }">
      <div class="space-y-5">
        <!-- <section
          class="rounded-[30px] border border-[#f1dce5] bg-white/85 shadow-[0_18px_50px_rgba(0,0,0,0.06)] backdrop-blur-xl p-6"
        >
          <p class="text-[11px] uppercase tracking-[0.24em] text-[#b38497] font-semibold">
            You Glow Babe Member
          </p>
          <h1 class="mt-3 font-playfair text-[34px] text-[#5f6670]">Welcome Back</h1>
          <p class="mt-2 text-[#7d838d]">
            Manage your account, check your rewards, and view your latest activity.
          </p>
        </section> -->

        <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="rounded-3xl border border-[#f0e4e9] bg-[#fffafb] p-5">
            <div class="text-[11px] uppercase tracking-[0.16em] text-[#b38497]">User Points</div>
            <div class="mt-2 text-3xl font-semibold text-[#5f6670]">
              {{ me?.user_points ?? 0 }}
            </div>
            <div class="mt-1 text-sm text-[#8a8f99]">Reward points</div>
          </div>

          <div class="rounded-3xl border border-[#f0e4e9] bg-[#fffafb] p-5">
            <div class="text-[11px] uppercase tracking-[0.16em] text-[#b38497]">Orders</div>
            <div class="mt-2 text-3xl font-semibold text-[#5f6670]">0</div>
            <div class="mt-1 text-sm text-[#8a8f99]">Recent purchases</div>
          </div>

          <div class="rounded-3xl border border-[#f0e4e9] bg-[#fffafb] p-5">
            <div class="text-[11px] uppercase tracking-[0.16em] text-[#b38497]">Status</div>
            <div class="mt-2 text-3xl font-semibold text-[#5f6670]">Active</div>
            <div class="mt-1 text-sm text-[#8a8f99]">Member account</div>
          </div>
        </section>

        <section
          class="rounded-[28px] border border-[#f1dce5] bg-white/85 shadow-[0_18px_50px_rgba(0,0,0,0.06)] p-6"
        >
          <h2 class="font-playfair text-[26px] text-[#5f6670]">Recent Activity</h2>
          <div class="mt-4 space-y-3">
            <!-- <div class="rounded-2xl border border-[#f0e4e9] bg-[#fffafb] px-4 py-4 text-[#6b7280]">
              You earned <span class="font-semibold text-[#e97aac]">25 points</span> from your
              recent purchase.
            </div> -->
            <div class="rounded-2xl border border-[#f0e4e9] bg-[#fffafb] px-4 py-4 text-[#6b7280]">
              Your account profile was updated successfully.
            </div>
          </div>
        </section>
      </div>
    </template>
  </UserLayout>
</template>

<script>
import UserLayout from '@/layouts/UserLayout.vue'

export default {
  name: 'UserDashboard',
  components: { UserLayout },
  data() {
    return {
      sidebarOpen: false,
    }
  },
}
</script>
