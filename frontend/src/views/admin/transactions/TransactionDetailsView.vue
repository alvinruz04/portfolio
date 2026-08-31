<template>
  <AdminLayout
    title="Transaction Details"
    :subtitle="transaction?.transaction_number || 'APESCON service transaction'"
    active="transactions"
    v-model:sidebar-open="sidebarOpen"
  >
    <div
      v-if="loading"
      class="flex min-h-96 items-center justify-center rounded-3xl border border-[#D8E4ED] bg-white"
    >
      <div class="text-center">
        <i class="fa-solid fa-spinner animate-spin text-[26px] text-[#025199]"></i>
        <p class="mt-3 font-nunito text-[13px] text-[#64748B]">Loading transaction details...</p>
      </div>
    </div>

    <div
      v-else-if="!transaction"
      class="rounded-3xl border border-[#F0D2D5] bg-[#FFF5F5] p-8 text-center"
    >
      <i class="fa-solid fa-circle-exclamation text-[24px] text-[#D51C26]"></i>
      <h2 class="mt-4 font-montserrat text-[17px] font-bold text-[#8E2530]">
        Transaction not found
      </h2>
      <button
        type="button"
        class="secondary-button mt-5"
        @click="router.push('/admin/transactions')"
      >
        Back to Transactions
      </button>
    </div>

    <div v-else class="space-y-6">
      <!-- Header -->
      <section
        class="relative overflow-hidden rounded-[26px] bg-linear-to-br from-[#062B4C] via-[#025199] to-[#073D68] p-5 text-white shadow-[0_22px_55px_rgba(5,35,61,0.18)] sm:p-7"
      >
        <div
          class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"
        ></div>
        <div
          class="pointer-events-none absolute -bottom-28 -left-20 h-72 w-72 rounded-full bg-[#F27C29]/18 blur-3xl"
        ></div>

        <div
          class="relative z-10 flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between"
        >
          <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-3">
              <span
                class="rounded-full border border-white/15 bg-white/10 px-3 py-1.5 font-montserrat text-[9px] font-semibold uppercase tracking-[0.14em] text-white/80"
              >
                {{ transaction.transaction_number }}
              </span>
              <span
                class="rounded-full border border-white/15 bg-white/10 px-3 py-1.5 font-montserrat text-[9px] font-semibold uppercase tracking-[0.14em] text-white/80"
              >
                {{ customerTypeLabel(transaction.customer_type) }}
              </span>
            </div>

            <h2
              class="mt-4 max-w-3xl font-montserrat text-[25px] font-bold leading-tight text-white sm:text-[31px]"
            >
              {{ transaction.customer_name }}
            </h2>
            <p class="mt-2 max-w-3xl font-nunito text-[13px] leading-6 text-white/68">
              {{ transaction.service_type_name }} · {{ fullSiteAddress }}
            </p>

            <div class="mt-5 flex flex-wrap items-center gap-2">
              <TransactionStatusBadge :status="transaction.transaction_status" />
              <PaymentStatusBadge :status="transaction.payment_status" />
              <span
                v-if="transaction.warranty_included"
                class="rounded-full border border-white/15 bg-white/10 px-3 py-1 font-montserrat text-[9px] font-semibold uppercase tracking-widest text-white/75"
              >
                Warranty to {{ formatDate(transaction.warranty_end_date) }}
              </span>
            </div>
          </div>

          <div class="flex flex-wrap gap-2">
            <button type="button" class="header-button" @click="router.push('/admin/transactions')">
              <i class="fa-solid fa-arrow-left"></i> Back
            </button>
            <button
              v-if="transaction.transaction_status !== 'cancelled'"
              type="button"
              class="header-button"
              @click="editTransaction"
            >
              <i class="fa-regular fa-pen-to-square"></i> Edit
            </button>
            <button
              v-if="
                transaction.transaction_status !== 'cancelled' &&
                transaction.transaction_status !== 'completed'
              "
              type="button"
              class="header-button header-button-danger"
              @click="openCancelModal"
            >
              <i class="fa-solid fa-ban"></i> Cancel Transaction
            </button>
          </div>
        </div>
      </section>

      <!-- Summary -->
      <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="summary-card">
          <p class="summary-label">Contract Amount</p>
          <p class="summary-value">{{ formatMoney(transaction.contract_amount) }}</p>
          <p class="summary-note">{{ transaction.contract_number || 'No contract number' }}</p>
        </article>
        <article class="summary-card">
          <p class="summary-label">Total Paid</p>
          <p class="summary-value">{{ formatMoney(transaction.total_paid) }}</p>
          <p class="summary-note">{{ formatMoney(transaction.balance) }} remaining balance</p>
        </article>
        <article class="summary-card">
          <p class="summary-label">Next Service</p>
          <p class="summary-value text-[20px]">
            {{ nextVisit ? formatDate(nextVisit.scheduled_date) : '—' }}
          </p>
          <p class="summary-note">{{ nextVisit?.title || 'No pending scheduled visit' }}</p>
        </article>
        <article class="summary-card">
          <p class="summary-label">Warranty</p>
          <p class="summary-value text-[20px]">
            {{ transaction.warranty_included ? formatDate(transaction.warranty_end_date) : 'None' }}
          </p>
          <p class="summary-note">
            {{
              transaction.warranty_included
                ? 'Based on signed contract dates'
                : 'No warranty recorded'
            }}
          </p>
        </article>
      </section>

      <!-- Main content -->
      <section class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        <div class="space-y-6 xl:col-span-8">
          <!-- Customer & contract -->
          <article class="content-card">
            <CardHeading icon="fa-solid fa-address-card" title="Customer & Contract" />

            <dl class="mt-5 grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
              <DetailItem label="Customer" :value="transaction.customer_name" />
              <DetailItem
                label="Customer Type"
                :value="customerTypeLabel(transaction.customer_type)"
              />
              <DetailItem
                label="Contact Person"
                :value="transaction.customer_contact_person || '—'"
              />
              <DetailItem
                label="Contact Number"
                :value="transaction.customer_contact_number || '—'"
              />
              <DetailItem
                label="Email Address"
                :value="transaction.customer_email_address || '—'"
              />
              <DetailItem label="TIN" :value="transaction.tin || '—'" />
              <DetailItem
                label="Property / Facility"
                :value="transaction.site_name || transaction.property_type || '—'"
              />
              <DetailItem label="Service Address" :value="fullSiteAddress" />
              <DetailItem label="Quotation Number" :value="transaction.quotation_number || '—'" />
              <DetailItem
                label="Quotation Amount"
                :value="
                  transaction.quotation_amount !== null
                    ? formatMoney(transaction.quotation_amount)
                    : '—'
                "
              />
              <DetailItem label="Contract Number" :value="transaction.contract_number || '—'" />
              <DetailItem label="Contract Date" :value="formatDate(transaction.contract_date)" />
              <DetailItem
                label="Contract Start"
                :value="formatDate(transaction.contract_start_date)"
              />
              <DetailItem label="Contract End" :value="formatDate(transaction.contract_end_date)" />
              <DetailItem
                label="Payment Terms"
                :value="paymentTermLabel(transaction.payment_terms)"
              />
            </dl>
          </article>

          <!-- Service -->
          <article class="content-card">
            <CardHeading icon="fa-solid fa-spray-can-sparkles" title="Service Details" />

            <dl class="mt-5 grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
              <DetailItem label="Service Type" :value="transaction.service_type_name" />
              <DetailItem label="Area Coverage" :value="areaLabel" />
              <DetailItem
                label="Personnel"
                :value="
                  transaction.personnel_count ? `${transaction.personnel_count} personnel` : '—'
                "
              />
              <DetailItem label="Assigned Team" :value="transaction.assigned_team || '—'" />
              <DetailItem label="Supervisor" :value="transaction.service_supervisor || '—'" />
              <DetailItem
                label="Frequency"
                :value="frequencyLabel(transaction.service_frequency)"
              />
            </dl>

            <div v-if="pests.length" class="mt-5 border-t border-[#E6EEF4] pt-5">
              <p class="detail-label">Target Pests</p>
              <div class="mt-2 flex flex-wrap gap-2">
                <span
                  v-for="pest in pests"
                  :key="pest.id"
                  class="rounded-full border border-[#D8E4ED] bg-[#F8FBFD] px-3 py-1.5 font-nunito text-[11px] font-semibold text-[#526274]"
                >
                  {{ pest.name }}
                </span>
              </div>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-4 border-t border-[#E6EEF4] pt-5">
              <TextBlock label="Treatment Method" :value="transaction.treatment_method" />
              <TextBlock
                label="Chemical / Technical Specification"
                :value="transaction.chemical_details"
              />
              <TextBlock label="Scope of Service" :value="transaction.service_scope" />
            </div>
          </article>

          <!-- Service schedule -->
          <article class="content-card">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <CardHeading icon="fa-regular fa-calendar-check" title="Service Schedule" />
              <span class="font-nunito text-[11px] text-[#94A3B8]">Calendar-ready records</span>
            </div>

            <div
              v-if="visits.length"
              class="mt-5 overflow-hidden rounded-[18px] border border-[#D8E4ED]"
            >
              <div class="divide-y divide-[#E6EEF4]">
                <div v-for="visit in visits" :key="visit.id" class="p-4 sm:p-5">
                  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-start gap-3">
                      <span
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#025199]/8 font-montserrat text-[10px] font-bold text-[#025199]"
                      >
                        {{ visit.visit_number || '•' }}
                      </span>
                      <div class="min-w-0">
                        <p class="font-montserrat text-[12px] font-semibold text-[#163A5F]">
                          {{ visit.title }}
                        </p>
                        <p class="mt-1 font-nunito text-[11px] text-[#64748B]">
                          {{ formatDate(visit.scheduled_date) }}
                          <template v-if="visit.scheduled_time">
                            · {{ formatTime(visit.scheduled_time) }}</template
                          >
                          <template v-if="visit.assigned_team">
                            · {{ visit.assigned_team }}</template
                          >
                        </p>
                      </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                      <span class="visit-status" :class="visitStatusClass(visit.status)">{{
                        visitStatusLabel(visit.status)
                      }}</span>
                      <button
                        v-if="canManageVisit(visit)"
                        type="button"
                        class="small-action"
                        @click="openRescheduleModal(visit)"
                      >
                        Reschedule
                      </button>
                      <button
                        v-if="canManageVisit(visit)"
                        type="button"
                        class="small-action small-action-primary"
                        @click="openCompleteModal(visit)"
                      >
                        Complete
                      </button>
                    </div>
                  </div>

                  <div
                    v-if="visit.actual_service_date || visit.completion_notes"
                    class="mt-3 rounded-xl bg-[#F8FBFD] px-4 py-3"
                  >
                    <p
                      v-if="visit.actual_service_date"
                      class="font-nunito text-[11px] text-[#526274]"
                    >
                      Actual service: <strong>{{ formatDate(visit.actual_service_date) }}</strong>
                    </p>
                    <p
                      v-if="visit.completion_notes"
                      class="mt-1 whitespace-pre-line font-nunito text-[11px] leading-5 text-[#64748B]"
                    >
                      {{ visit.completion_notes }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <p
              v-else
              class="mt-5 rounded-2xl bg-[#F8FBFD] p-5 text-center font-nunito text-[12px] text-[#94A3B8]"
            >
              No service visits are recorded.
            </p>
          </article>
        </div>

        <aside class="space-y-6 xl:col-span-4">
          <!-- Payments -->
          <article class="content-card">
            <div class="flex items-center justify-between gap-3">
              <CardHeading icon="fa-solid fa-peso-sign" title="Payments" />
              <button
                v-if="
                  transaction.transaction_status !== 'cancelled' && Number(transaction.balance) > 0
                "
                type="button"
                class="small-action small-action-primary"
                @click="openPaymentModal"
              >
                <i class="fa-solid fa-plus"></i> Record
              </button>
            </div>

            <div class="mt-5 rounded-2xl bg-[#F8FBFD] p-4">
              <div class="flex items-center justify-between gap-4">
                <span class="font-nunito text-[11px] text-[#64748B]">Paid</span>
                <strong class="font-montserrat text-[12px] text-[#163A5F]">{{
                  formatMoney(transaction.total_paid)
                }}</strong>
              </div>
              <div class="mt-2 flex items-center justify-between gap-4">
                <span class="font-nunito text-[11px] text-[#64748B]">Balance</span>
                <strong class="font-montserrat text-[12px] text-[#B4232D]">{{
                  formatMoney(transaction.balance)
                }}</strong>
              </div>
            </div>

            <div v-if="payments.length" class="mt-4 space-y-3">
              <div
                v-for="payment in payments"
                :key="payment.id"
                class="rounded-[15px] border border-[#D8E4ED] p-3.5"
                :class="payment.status === 'voided' ? 'opacity-55' : ''"
              >
                <div class="flex items-start justify-between gap-3">
                  <div>
                    <p class="font-montserrat text-[12px] font-semibold text-[#163A5F]">
                      {{ formatMoney(payment.amount) }}
                    </p>
                    <p class="mt-1 font-nunito text-[10px] text-[#64748B]">
                      {{ formatDate(payment.payment_date) }} ·
                      {{ paymentMethodLabel(payment.payment_method) }}
                    </p>
                  </div>
                  <span
                    v-if="payment.status === 'voided'"
                    class="rounded-full bg-[#FFF5F5] px-2 py-1 font-montserrat text-[8px] font-semibold uppercase tracking-widest text-[#B4232D]"
                    >Voided</span
                  >
                  <button
                    v-else
                    type="button"
                    class="text-[10px] text-[#94A3B8] hover:text-[#D51C26]"
                    title="Void payment"
                    @click="openVoidPaymentModal(payment)"
                  >
                    <i class="fa-solid fa-ban"></i>
                  </button>
                </div>
                <p
                  v-if="payment.reference_number"
                  class="mt-2 font-nunito text-[10px] text-[#64748B]"
                >
                  Ref: {{ payment.reference_number }}
                </p>
                <p
                  v-if="payment.void_reason"
                  class="mt-2 font-nunito text-[10px] leading-4 text-[#B4232D]"
                >
                  {{ payment.void_reason }}
                </p>
              </div>
            </div>
            <p v-else class="mt-4 text-center font-nunito text-[11px] text-[#94A3B8]">
              No payments recorded yet.
            </p>
          </article>

          <!-- Warranty -->
          <article class="content-card">
            <CardHeading icon="fa-solid fa-shield-halved" title="Warranty" />
            <template v-if="transaction.warranty_included">
              <div class="mt-5 grid grid-cols-2 gap-3">
                <div class="rounded-[14px] bg-[#F8FBFD] p-3">
                  <p class="detail-label">Start</p>
                  <p class="mt-1 font-nunito text-[11px] font-semibold text-[#526274]">
                    {{ formatDate(transaction.warranty_start_date) }}
                  </p>
                </div>
                <div class="rounded-[14px] bg-[#F8FBFD] p-3">
                  <p class="detail-label">End</p>
                  <p class="mt-1 font-nunito text-[11px] font-semibold text-[#526274]">
                    {{ formatDate(transaction.warranty_end_date) }}
                  </p>
                </div>
              </div>
              <p
                v-if="transaction.warranty_notes"
                class="mt-4 whitespace-pre-line font-nunito text-[11px] leading-5 text-[#64748B]"
              >
                {{ transaction.warranty_notes }}
              </p>
            </template>
            <p
              v-else
              class="mt-5 rounded-[14px] bg-[#F8FBFD] p-4 font-nunito text-[11px] text-[#64748B]"
            >
              No warranty coverage was recorded for this transaction.
            </p>
          </article>

          <!-- Government -->
          <article v-if="transaction.customer_type === 'government'" class="content-card">
            <CardHeading icon="fa-solid fa-landmark" title="Government References" />
            <dl class="mt-5 space-y-4">
              <DetailItem
                label="Project / Contract"
                :value="transaction.government_project_title || '—'"
              />
              <DetailItem
                label="Procurement Reference"
                :value="transaction.government_reference || '—'"
              />
              <DetailItem
                label="PhilGEPS Reference"
                :value="transaction.philgeps_reference || '—'"
              />
              <DetailItem
                label="Purchase Order"
                :value="transaction.purchase_order_number || '—'"
              />
              <DetailItem
                label="Notice to Proceed"
                :value="formatDate(transaction.notice_to_proceed_date)"
              />
              <DetailItem
                label="Government Contact"
                :value="transaction.government_representative || '—'"
              />
            </dl>
          </article>

          <!-- Activity -->
          <article class="content-card">
            <CardHeading icon="fa-solid fa-clock-rotate-left" title="Activity" />
            <div v-if="activity.length" class="mt-5 space-y-4">
              <div v-for="item in activity" :key="item.id" class="flex gap-3">
                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[#025199]"></span>
                <div class="min-w-0">
                  <p class="font-nunito text-[11px] leading-5 text-[#526274]">
                    {{ item.description }}
                  </p>
                  <p class="mt-1 font-nunito text-[9px] text-[#94A3B8]">
                    {{ formatDateTime(item.created_at) }} · {{ item.actor_name || 'System' }}
                  </p>
                </div>
              </div>
            </div>
            <p v-else class="mt-5 font-nunito text-[11px] text-[#94A3B8]">
              No activity history yet.
            </p>
          </article>
        </aside>
      </section>
    </div>

    <!-- Payment modal -->
    <Teleport to="body">
      <div v-if="showPaymentModal" class="modal-root">
        <div class="modal-backdrop" @click="closePaymentModal"></div>
        <div class="modal-card">
          <ModalHeader
            icon="fa-solid fa-peso-sign"
            title="Record Payment"
            subtitle="Add a payment against the current outstanding balance."
            @close="closePaymentModal"
          />
          <form class="p-5 sm:p-6" @submit.prevent="recordPayment">
            <div class="rounded-[14px] bg-[#F8FBFD] p-4">
              <div class="flex items-center justify-between">
                <span class="font-nunito text-[11px] text-[#64748B]">Outstanding balance</span>
                <strong class="font-montserrat text-[13px] text-[#B4232D]">{{
                  formatMoney(transaction?.balance)
                }}</strong>
              </div>
            </div>
            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <label class="modal-label">Amount *</label>
                <input
                  v-model="paymentForm.amount"
                  class="modal-input"
                  type="number"
                  min="0.01"
                  :max="transaction?.balance"
                  step="0.01"
                  required
                />
              </div>
              <div>
                <label class="modal-label">Payment Date *</label>
                <input
                  v-model="paymentForm.payment_date"
                  class="modal-input"
                  type="date"
                  required
                />
              </div>
              <div>
                <label class="modal-label">Method *</label>
                <select v-model="paymentForm.payment_method" class="modal-input">
                  <option value="cash">Cash</option>
                  <option value="bank_transfer">Bank Transfer</option>
                  <option value="gcash">GCash</option>
                  <option value="check">Check</option>
                  <option value="government_disbursement">Government Disbursement</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div>
                <label class="modal-label">Reference / Receipt No.</label>
                <input
                  v-model.trim="paymentForm.reference_number"
                  class="modal-input"
                  type="text"
                  maxlength="150"
                />
              </div>
              <div class="sm:col-span-2">
                <label class="modal-label">Remarks</label>
                <textarea
                  v-model.trim="paymentForm.remarks"
                  class="modal-input min-h-20 resize-y"
                  maxlength="500"
                ></textarea>
              </div>
            </div>
            <ModalActions
              :loading="modalSaving"
              confirm-text="Record Payment"
              @cancel="closePaymentModal"
            />
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Reason modal: cancel transaction / void payment -->
    <Teleport to="body">
      <div v-if="reasonModal.show" class="modal-root">
        <div class="modal-backdrop" @click="closeReasonModal"></div>
        <div class="modal-card">
          <ModalHeader
            :icon="reasonModal.type === 'cancel' ? 'fa-solid fa-ban' : 'fa-solid fa-circle-minus'"
            :title="reasonModal.type === 'cancel' ? 'Cancel Transaction' : 'Void Payment'"
            :subtitle="
              reasonModal.type === 'cancel'
                ? 'The transaction is preserved, but future scheduled visits are cancelled.'
                : 'The original payment remains in history and is marked voided.'
            "
            danger
            @close="closeReasonModal"
          />
          <form class="p-5 sm:p-6" @submit.prevent="submitReasonAction">
            <label class="modal-label">Reason *</label>
            <textarea
              v-model.trim="reasonModal.reason"
              class="modal-input min-h-28 resize-y"
              maxlength="500"
              required
              placeholder="Enter a clear reason for the change"
            ></textarea>
            <ModalActions
              :loading="modalSaving"
              :confirm-text="reasonModal.type === 'cancel' ? 'Cancel Transaction' : 'Void Payment'"
              danger
              @cancel="closeReasonModal"
            />
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Reschedule modal -->
    <Teleport to="body">
      <div v-if="rescheduleModal.show" class="modal-root">
        <div class="modal-backdrop" @click="closeRescheduleModal"></div>
        <div class="modal-card">
          <ModalHeader
            icon="fa-regular fa-calendar"
            title="Reschedule Service Visit"
            :subtitle="rescheduleModal.visit?.title || ''"
            @close="closeRescheduleModal"
          />
          <form class="p-5 sm:p-6" @submit.prevent="rescheduleVisit">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <label class="modal-label">New Date *</label>
                <input
                  v-model="rescheduleModal.scheduled_date"
                  class="modal-input"
                  type="date"
                  required
                />
              </div>
              <div>
                <label class="modal-label">Time</label>
                <input v-model="rescheduleModal.scheduled_time" class="modal-input" type="time" />
              </div>
              <div class="sm:col-span-2">
                <label class="modal-label">Reason *</label>
                <textarea
                  v-model.trim="rescheduleModal.reason"
                  class="modal-input min-h-24 resize-y"
                  maxlength="500"
                  required
                ></textarea>
              </div>
            </div>
            <ModalActions
              :loading="modalSaving"
              confirm-text="Save New Schedule"
              @cancel="closeRescheduleModal"
            />
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Complete modal -->
    <Teleport to="body">
      <div v-if="completeModal.show" class="modal-root">
        <div class="modal-backdrop" @click="closeCompleteModal"></div>
        <div class="modal-card">
          <ModalHeader
            icon="fa-solid fa-circle-check"
            title="Complete Service Visit"
            :subtitle="completeModal.visit?.title || ''"
            @close="closeCompleteModal"
          />
          <form class="p-5 sm:p-6" @submit.prevent="completeVisit">
            <label class="modal-label">Actual Service Date *</label>
            <input
              v-model="completeModal.actual_service_date"
              class="modal-input"
              type="date"
              required
            />
            <label class="modal-label mt-4">Completion Notes</label>
            <textarea
              v-model.trim="completeModal.completion_notes"
              class="modal-input min-h-28 resize-y"
              maxlength="10000"
              placeholder="Work performed, findings, customer instructions or important notes"
            ></textarea>
            <ModalActions
              :loading="modalSaving"
              confirm-text="Mark Completed"
              @cancel="closeCompleteModal"
            />
          </form>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup>
import { computed, defineComponent, h, inject, onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/layouts/AdminLayout.vue'
import PaymentStatusBadge from '@/components/admin/transactions/PaymentStatusBadge.vue'
import TransactionStatusBadge from '@/components/admin/transactions/TransactionStatusBadge.vue'
import { transactionService } from '@/services/transactionService'

const CardHeading = defineComponent({
  props: { icon: String, title: String },
  setup(props) {
    return () =>
      h('div', { class: 'flex items-center gap-3' }, [
        h(
          'span',
          {
            class:
              'flex h-9 w-9 items-center justify-center rounded-xl bg-[#025199]/8 text-[#025199]',
          },
          [h('i', { class: `${props.icon} text-[12px]` })],
        ),
        h('h3', { class: 'font-montserrat text-[14px] font-bold text-[#163A5F]' }, props.title),
      ])
  },
})

const DetailItem = defineComponent({
  props: { label: String, value: [String, Number] },
  setup(props) {
    return () =>
      h('div', null, [
        h('dt', { class: 'detail-label' }, props.label),
        h(
          'dd',
          { class: 'mt-1.5 whitespace-pre-line font-nunito text-[12px] leading-5 text-[#526274]' },
          String(props.value ?? '—'),
        ),
      ])
  },
})

const TextBlock = defineComponent({
  props: { label: String, value: String },
  setup(props) {
    return () =>
      h('div', null, [
        h('p', { class: 'detail-label' }, props.label),
        h(
          'p',
          {
            class:
              'mt-2 whitespace-pre-line rounded-[14px] bg-[#F8FBFD] p-4 font-nunito text-[12px] leading-6 text-[#526274]',
          },
          props.value || '—',
        ),
      ])
  },
})

const ModalHeader = defineComponent({
  emits: ['close'],
  props: { icon: String, title: String, subtitle: String, danger: Boolean },
  setup(props, { emit }) {
    return () =>
      h(
        'div',
        { class: 'flex items-start justify-between gap-4 border-b border-[#E6EEF4] p-5 sm:p-6' },
        [
          h('div', { class: 'flex items-start gap-3' }, [
            h(
              'span',
              {
                class: props.danger
                  ? 'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#FFF5F5] text-[#D51C26]'
                  : 'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#025199]/8 text-[#025199]',
              },
              [h('i', { class: `${props.icon} text-[13px]` })],
            ),
            h('div', null, [
              h(
                'h3',
                { class: 'font-montserrat text-[15px] font-bold text-[#163A5F]' },
                props.title,
              ),
              props.subtitle
                ? h(
                    'p',
                    { class: 'mt-1 font-nunito text-[11px] leading-5 text-[#64748B]' },
                    props.subtitle,
                  )
                : null,
            ]),
          ]),
          h(
            'button',
            {
              type: 'button',
              class:
                'flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-[#94A3B8] hover:bg-[#F8FBFD] hover:text-[#025199]',
              onClick: () => emit('close'),
            },
            [h('i', { class: 'fa-solid fa-xmark' })],
          ),
        ],
      )
  },
})

const ModalActions = defineComponent({
  emits: ['cancel'],
  props: { loading: Boolean, confirmText: String, danger: Boolean },
  setup(props, { emit }) {
    return () =>
      h('div', { class: 'mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end' }, [
        h(
          'button',
          {
            type: 'button',
            disabled: props.loading,
            class: 'secondary-button',
            onClick: () => emit('cancel'),
          },
          'Cancel',
        ),
        h(
          'button',
          {
            type: 'submit',
            disabled: props.loading,
            class: props.danger ? 'danger-button' : 'primary-button',
          },
          [
            props.loading ? h('i', { class: 'fa-solid fa-spinner animate-spin' }) : null,
            h('span', null, props.loading ? 'Processing...' : props.confirmText || 'Confirm'),
          ],
        ),
      ])
  },
})

const route = useRoute()
const router = useRouter()
const showToast = inject('showToast', null)

const sidebarOpen = ref(false)
const loading = ref(true)
const modalSaving = ref(false)
const transactionData = ref(null)
const showPaymentModal = ref(false)

const paymentForm = reactive({
  amount: '',
  payment_date: '',
  payment_method: 'cash',
  reference_number: '',
  remarks: '',
})

const reasonModal = reactive({
  show: false,
  type: '',
  reason: '',
  payment: null,
})

const rescheduleModal = reactive({
  show: false,
  visit: null,
  scheduled_date: '',
  scheduled_time: '',
  reason: '',
})

const completeModal = reactive({
  show: false,
  visit: null,
  actual_service_date: '',
  completion_notes: '',
})

const transactionId = computed(() => Number(route.params.id || 0))
const transaction = computed(() => transactionData.value?.transaction || null)
const pests = computed(() => transactionData.value?.pests || [])
const payments = computed(() => transactionData.value?.payments || [])
const visits = computed(() => transactionData.value?.visits || [])
const activity = computed(() => transactionData.value?.activity || [])

const nextVisit = computed(
  () => visits.value.find((visit) => ['scheduled', 'rescheduled'].includes(visit.status)) || null,
)

const fullSiteAddress = computed(() => {
  if (!transaction.value) return '—'
  return [
    transaction.value.site_name,
    transaction.value.address_line,
    transaction.value.barangay,
    transaction.value.city,
    transaction.value.province,
  ]
    .filter(Boolean)
    .join(', ')
})

const areaLabel = computed(() => {
  if (transaction.value?.area_coverage === null || transaction.value?.area_coverage === undefined)
    return '—'
  return `${Number(transaction.value.area_coverage).toLocaleString('en-PH')} ${transaction.value.area_unit || ''}`.trim()
})

const todayYmd = () => {
  const now = new Date()
  const year = now.getFullYear()
  const month = String(now.getMonth() + 1).padStart(2, '0')
  const day = String(now.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const loadTransaction = async () => {
  loading.value = true

  try {
    transactionData.value = await transactionService.show(transactionId.value)
  } catch (error) {
    console.error('Unable to load transaction:', error)
    showToast?.(error?.message || 'Unable to load transaction details.', 'error')
    transactionData.value = null
  } finally {
    loading.value = false
  }
}

const editTransaction = () => router.push(`/admin/transactions/${transactionId.value}/edit`)

const openPaymentModal = () => {
  paymentForm.amount = transaction.value?.balance || ''
  paymentForm.payment_date = todayYmd()
  paymentForm.payment_method =
    transaction.value?.customer_type === 'government' ? 'government_disbursement' : 'cash'
  paymentForm.reference_number = ''
  paymentForm.remarks = ''
  showPaymentModal.value = true
}

const closePaymentModal = () => {
  if (modalSaving.value) return
  showPaymentModal.value = false
}

const recordPayment = async () => {
  if (modalSaving.value) return
  modalSaving.value = true

  try {
    const data = await transactionService.recordPayment({
      transaction_id: transactionId.value,
      amount: Number(paymentForm.amount || 0),
      payment_date: paymentForm.payment_date,
      payment_method: paymentForm.payment_method,
      reference_number: paymentForm.reference_number || null,
      remarks: paymentForm.remarks || null,
    })

    showToast?.(data.message || 'Payment recorded successfully.', 'success')
    showPaymentModal.value = false
    await loadTransaction()
  } catch (error) {
    showToast?.(error?.message || 'Unable to record payment.', 'error', 5000)
  } finally {
    modalSaving.value = false
  }
}

const openCancelModal = () => {
  reasonModal.show = true
  reasonModal.type = 'cancel'
  reasonModal.reason = ''
  reasonModal.payment = null
}

const openVoidPaymentModal = (payment) => {
  reasonModal.show = true
  reasonModal.type = 'void-payment'
  reasonModal.reason = ''
  reasonModal.payment = payment
}

const closeReasonModal = () => {
  if (modalSaving.value) return
  reasonModal.show = false
  reasonModal.type = ''
  reasonModal.reason = ''
  reasonModal.payment = null
}

const submitReasonAction = async () => {
  if (modalSaving.value || !reasonModal.reason.trim()) return
  modalSaving.value = true

  try {
    let data

    if (reasonModal.type === 'cancel') {
      data = await transactionService.cancel(transactionId.value, reasonModal.reason.trim())
    } else {
      data = await transactionService.voidPayment(reasonModal.payment.id, reasonModal.reason.trim())
    }

    showToast?.(data.message || 'Change saved successfully.', 'success')
    reasonModal.show = false
    reasonModal.type = ''
    reasonModal.reason = ''
    reasonModal.payment = null
    await loadTransaction()
  } catch (error) {
    showToast?.(error?.message || 'Unable to complete the requested action.', 'error', 5000)
  } finally {
    modalSaving.value = false
  }
}

const canManageVisit = (visit) =>
  transaction.value?.transaction_status !== 'cancelled' &&
  !['completed', 'cancelled'].includes(visit.status)

const openRescheduleModal = (visit) => {
  rescheduleModal.show = true
  rescheduleModal.visit = visit
  rescheduleModal.scheduled_date = visit.scheduled_date
  rescheduleModal.scheduled_time = visit.scheduled_time
    ? String(visit.scheduled_time).slice(0, 5)
    : ''
  rescheduleModal.reason = ''
}

const closeRescheduleModal = () => {
  if (modalSaving.value) return
  rescheduleModal.show = false
  rescheduleModal.visit = null
  rescheduleModal.reason = ''
}

const rescheduleVisit = async () => {
  if (modalSaving.value || !rescheduleModal.visit) return
  modalSaving.value = true

  try {
    const data = await transactionService.rescheduleVisit({
      visit_id: rescheduleModal.visit.id,
      scheduled_date: rescheduleModal.scheduled_date,
      scheduled_time: rescheduleModal.scheduled_time || null,
      reason: rescheduleModal.reason,
    })

    showToast?.(data.message || 'Service visit rescheduled.', 'success')
    rescheduleModal.show = false
    rescheduleModal.visit = null
    rescheduleModal.reason = ''
    await loadTransaction()
  } catch (error) {
    showToast?.(error?.message || 'Unable to reschedule the service visit.', 'error', 5000)
  } finally {
    modalSaving.value = false
  }
}

const openCompleteModal = (visit) => {
  completeModal.show = true
  completeModal.visit = visit
  completeModal.actual_service_date = todayYmd()
  completeModal.completion_notes = ''
}

const closeCompleteModal = () => {
  if (modalSaving.value) return
  completeModal.show = false
  completeModal.visit = null
  completeModal.completion_notes = ''
}

const completeVisit = async () => {
  if (modalSaving.value || !completeModal.visit) return
  modalSaving.value = true

  try {
    const data = await transactionService.completeVisit({
      visit_id: completeModal.visit.id,
      actual_service_date: completeModal.actual_service_date,
      completion_notes: completeModal.completion_notes || null,
    })

    showToast?.(data.message || 'Service visit completed.', 'success')
    completeModal.show = false
    completeModal.visit = null
    completeModal.completion_notes = ''
    await loadTransaction()
  } catch (error) {
    showToast?.(error?.message || 'Unable to complete the service visit.', 'error', 5000)
  } finally {
    modalSaving.value = false
  }
}

const parseLocalDate = (value) => {
  if (!value) return null
  const [year, month, day] = String(value).slice(0, 10).split('-').map(Number)
  return year && month && day ? new Date(year, month - 1, day, 12, 0, 0) : null
}

const formatDate = (value) => {
  const date = parseLocalDate(value)
  if (!date) return '—'
  return new Intl.DateTimeFormat('en-PH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  }).format(date)
}

const formatDateTime = (value) => {
  if (!value) return '—'
  const safe = String(value).replace(' ', 'T') + 'Z'
  const date = new Date(safe)
  if (Number.isNaN(date.getTime())) return value
  return new Intl.DateTimeFormat('en-PH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    timeZone: 'Asia/Manila',
  }).format(date)
}

const formatTime = (value) => {
  const [hour, minute] = String(value || '')
    .split(':')
    .map(Number)
  if (!Number.isFinite(hour)) return '—'
  const date = new Date(2000, 0, 1, hour, minute || 0)
  return new Intl.DateTimeFormat('en-PH', { hour: 'numeric', minute: '2-digit' }).format(date)
}

const formatMoney = (value) =>
  new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
    minimumFractionDigits: 2,
  }).format(Number(value || 0))

const customerTypeLabel = (value) =>
  ({
    residential: 'Residential',
    commercial: 'Commercial',
    industrial: 'Industrial',
    institutional: 'Institutional',
    government: 'Government',
    agricultural: 'Agricultural',
  })[value] ||
  value ||
  'Customer'

const paymentTermLabel = (value) =>
  ({
    full_before_service: 'Full Payment Before Service',
    full_after_service: 'Full Payment After Service',
    partial_installment: 'Partial / Installment',
    per_visit: 'Per Service Visit',
    government_processing: 'Government Processing',
    other: 'Other',
  })[value] ||
  value ||
  '—'

const paymentMethodLabel = (value) =>
  ({
    cash: 'Cash',
    bank_transfer: 'Bank Transfer',
    gcash: 'GCash',
    check: 'Check',
    government_disbursement: 'Government Disbursement',
    other: 'Other',
  })[value] ||
  value ||
  '—'

const frequencyLabel = (value) =>
  ({
    one_time: 'One-Time',
    weekly: 'Weekly',
    monthly: 'Monthly',
    quarterly: 'Quarterly',
    semi_annual: 'Semi-Annual',
    annual: 'Annual',
    custom: 'Custom Interval',
  })[value] ||
  value ||
  '—'

const visitStatusLabel = (value) =>
  ({
    scheduled: 'Scheduled',
    rescheduled: 'Rescheduled',
    completed: 'Completed',
    cancelled: 'Cancelled',
    missed: 'Missed',
  })[value] || value

const visitStatusClass = (value) =>
  ({
    scheduled: 'border-[#BDD5EB] bg-[#F1F7FC] text-[#025199]',
    rescheduled: 'border-[#F1DDC9] bg-[#FFF8F1] text-[#9A5525]',
    completed: 'border-[#B9DFC9] bg-[#F0FAF4] text-[#267149]',
    cancelled: 'border-[#F0D2D5] bg-[#FFF5F5] text-[#B4232D]',
    missed: 'border-[#F0D2D5] bg-[#FFF5F5] text-[#B4232D]',
  })[value] || 'border-[#D8E4ED] bg-[#F8FBFD] text-[#64748B]'

onMounted(loadTransaction)
</script>

<style>
.content-card,
.summary-card {
  border: 1px solid #d8e4ed;
  background: #fff;
  box-shadow: 0 12px 32px rgba(2, 81, 153, 0.05);
}

.content-card {
  border-radius: 24px;
  padding: 1.25rem;
}

.summary-card {
  border-radius: 20px;
  padding: 1.125rem;
}

.summary-label,
.detail-label,
.modal-label {
  font-family: 'Montserrat', sans-serif;
  font-size: 0.5625rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #64748b;
}

.summary-value {
  margin-top: 0.6rem;
  font-family: 'Montserrat', sans-serif;
  font-size: 1.45rem;
  font-weight: 700;
  color: #163a5f;
}

.summary-note {
  margin-top: 0.35rem;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 0.6875rem;
  color: #94a3b8;
}

.header-button,
.secondary-button,
.primary-button,
.danger-button,
.small-action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  border-radius: 0.75rem;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.09em;
  transition: all 180ms ease;
}

.header-button {
  min-height: 2.5rem;
  border: 1px solid rgba(255, 255, 255, 0.16);
  background: rgba(255, 255, 255, 0.1);
  padding: 0.55rem 0.8rem;
  font-size: 0.5625rem;
  color: rgba(255, 255, 255, 0.86);
}

.header-button:hover {
  background: rgba(255, 255, 255, 0.16);
  color: #fff;
}

.header-button-danger:hover {
  border-color: rgba(255, 200, 200, 0.35);
  background: rgba(213, 28, 38, 0.3);
}

.secondary-button,
.primary-button,
.danger-button {
  min-height: 2.75rem;
  padding: 0.7rem 1rem;
  font-size: 0.5625rem;
}

.secondary-button {
  border: 1px solid #cbd8e2;
  background: #fff;
  color: #64748b;
}

.primary-button {
  border: 1px solid #025199;
  background: #025199;
  color: #fff;
}

.danger-button {
  border: 1px solid #d51c26;
  background: #d51c26;
  color: #fff;
}

.secondary-button:hover:not(:disabled) {
  border-color: #025199;
  color: #025199;
}

.primary-button:hover:not(:disabled) {
  background: #063f73;
}

.danger-button:hover:not(:disabled) {
  background: #b91f28;
}

.small-action {
  min-height: 2rem;
  border: 1px solid #d8e4ed;
  background: #fff;
  padding: 0.4rem 0.65rem;
  font-size: 0.5rem;
  color: #64748b;
}

.small-action:hover {
  border-color: #025199;
  color: #025199;
}

.small-action-primary {
  border-color: rgba(2, 81, 153, 0.25);
  background: #f2f7fb;
  color: #025199;
}

.visit-status {
  display: inline-flex;
  border-width: 1px;
  border-radius: 9999px;
  padding: 0.3rem 0.6rem;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.5rem;
  font-weight: 700;
  letter-spacing: 0.09em;
  text-transform: uppercase;
}

.modal-root {
  position: fixed;
  inset: 0;
  z-index: 9998;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}

.modal-backdrop {
  position: absolute;
  inset: 0;
  background: rgba(7, 26, 43, 0.62);
  backdrop-filter: blur(4px);
}

.modal-card {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 40rem;
  overflow: hidden;
  border: 1px solid #d8e4ed;
  border-radius: 1.5rem;
  background: #fff;
  box-shadow: 0 30px 90px rgba(5, 35, 61, 0.3);
}

.modal-input {
  margin-top: 0.5rem;
  width: 100%;
  min-height: 2.75rem;
  border: 1px solid #d8e4ed;
  border-radius: 0.8rem;
  background: #fff;
  padding: 0.7rem 0.8rem;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 0.8rem;
  color: #334155;
  outline: none;
}

.modal-input:focus {
  border-color: #025199;
  box-shadow: 0 0 0 4px rgba(2, 81, 153, 0.08);
}

@media (min-width: 640px) {
  .content-card {
    padding: 1.5rem;
  }
}
</style>
