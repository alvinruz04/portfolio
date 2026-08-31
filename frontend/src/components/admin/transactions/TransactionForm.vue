<template>
  <form class="space-y-6" @submit.prevent="submitForm">
    <!-- Loading reference data -->
    <div
      v-if="isLoadingReference"
      class="flex min-h-56 items-center justify-center rounded-3xl border border-[#D8E4ED] bg-white shadow-[0_12px_32px_rgba(2,81,153,0.05)]"
    >
      <div class="text-center">
        <i class="fa-solid fa-spinner animate-spin text-[24px] text-[#025199]"></i>
        <p class="mt-3 font-nunito text-[13px] text-[#64748B]">Loading transaction form...</p>
      </div>
    </div>

    <template v-else>
      <!-- Validation summary -->
      <div
        v-if="validationErrors.length"
        class="rounded-[18px] border border-[#F0D2D5] bg-[#FFF5F5] p-4"
        role="alert"
      >
        <div class="flex items-start gap-3">
          <i class="fa-solid fa-circle-exclamation mt-0.5 text-[#D51C26]"></i>
          <div>
            <p
              class="font-montserrat text-[11px] font-semibold uppercase tracking-[0.12em] text-[#8E2530]"
            >
              Please review the following
            </p>
            <ul class="mt-2 list-disc space-y-1 pl-5 font-nunito text-[13px] text-[#8E2530]">
              <li v-for="error in validationErrors" :key="error">{{ error }}</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Customer -->
      <section class="form-card">
        <FormHeading
          number="01"
          icon="fa-solid fa-user-group"
          title="Customer & Service Location"
          description="Connect this transaction to an existing APESCON customer or create a new customer profile."
        />

        <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
          <div class="lg:col-span-2">
            <label class="field-label">Customer Record</label>
            <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-2">
              <button
                type="button"
                class="choice-card"
                :class="form.customer_mode === 'existing' ? 'choice-card-active' : ''"
                @click="setCustomerMode('existing')"
              >
                <span class="choice-icon"><i class="fa-solid fa-address-book"></i></span>
                <span>
                  <strong>Existing Customer</strong>
                  <small>Select a customer already saved in the database.</small>
                </span>
              </button>

              <button
                type="button"
                class="choice-card"
                :class="form.customer_mode === 'new' ? 'choice-card-active' : ''"
                @click="setCustomerMode('new')"
              >
                <span class="choice-icon"><i class="fa-solid fa-user-plus"></i></span>
                <span>
                  <strong>New Customer</strong>
                  <small>Create the customer while creating this transaction.</small>
                </span>
              </button>
            </div>
          </div>

          <template v-if="form.customer_mode === 'existing'">
            <div class="lg:col-span-2">
              <label for="customer-id" class="field-label">Customer *</label>
              <select
                id="customer-id"
                v-model.number="form.customer_id"
                class="field-input"
                @change="onCustomerChange"
              >
                <option :value="0">Select customer</option>
                <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                  {{ customer.display_name }} — {{ customerTypeLabel(customer.customer_type) }}
                </option>
              </select>
            </div>

            <div v-if="selectedCustomer" class="lg:col-span-2">
              <div class="rounded-[18px] border border-[#D8E4ED] bg-[#F8FBFD] p-4 sm:p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                  <div>
                    <p class="font-montserrat text-[13px] font-semibold text-[#163A5F]">
                      {{ selectedCustomer.display_name }}
                    </p>
                    <p class="mt-1 font-nunito text-[12px] text-[#64748B]">
                      {{ customerTypeLabel(selectedCustomer.customer_type) }}
                      <template v-if="selectedCustomer.contact_number">
                        · {{ selectedCustomer.contact_number }}</template
                      >
                      <template v-if="selectedCustomer.email_address">
                        · {{ selectedCustomer.email_address }}</template
                      >
                    </p>
                  </div>
                  <span
                    class="rounded-full bg-[#025199]/8 px-3 py-1.5 font-montserrat text-[9px] font-semibold uppercase tracking-[0.12em] text-[#025199]"
                  >
                    Existing Profile
                  </span>
                </div>
              </div>
            </div>
          </template>

          <template v-else>
            <div>
              <label for="new-customer-type" class="field-label"
                >Customer Type <span class="text-red-500">*</span></label
              >
              <select
                id="new-customer-type"
                v-model="form.customer.customer_type"
                class="field-input"
              >
                <option
                  v-for="option in customerTypeOptions"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
            </div>

            <div>
              <label for="new-customer-name" class="field-label"
                >Customer / Organization Name <span class="text-red-500">*</span></label
              >
              <input
                id="new-customer-name"
                v-model.trim="form.customer.display_name"
                class="field-input"
                type="text"
                maxlength="190"
                placeholder="Juan Dela Cruz or Agency / Company name"
              />
            </div>

            <div>
              <label for="customer-contact-person" class="field-label">Contact Person</label>
              <input
                id="customer-contact-person"
                v-model.trim="form.customer.contact_person"
                class="field-input"
                type="text"
                maxlength="150"
                placeholder="Primary contact person"
              />
            </div>

            <div>
              <label for="customer-contact-number" class="field-label">Contact Number</label>
              <input
                id="customer-contact-number"
                v-model.trim="form.customer.contact_number"
                class="field-input"
                type="text"
                maxlength="50"
                placeholder="09xx xxx xxxx"
              />
            </div>

            <div>
              <label for="customer-email" class="field-label">Email Address</label>
              <input
                id="customer-email"
                v-model.trim="form.customer.email_address"
                class="field-input"
                type="email"
                maxlength="191"
                placeholder="customer@example.com"
              />
            </div>

            <div>
              <label for="customer-tin" class="field-label">TIN</label>
              <input
                id="customer-tin"
                v-model.trim="form.customer.tin"
                class="field-input"
                type="text"
                maxlength="50"
                placeholder="Optional"
              />
            </div>

            <div class="lg:col-span-2">
              <label for="billing-address" class="field-label">Billing Address</label>
              <textarea
                id="billing-address"
                v-model.trim="form.customer.billing_address"
                class="field-input min-h-24 resize-y"
                maxlength="2000"
                placeholder="Billing address, if different from the service location"
              ></textarea>
            </div>
          </template>

          <!-- Site selection -->
          <div class="lg:col-span-2 border-t border-[#E6EEF4] pt-5">
            <label class="field-label">Service Location</label>

            <template v-if="form.customer_mode === 'existing' && selectedCustomer?.sites?.length">
              <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <button
                  type="button"
                  class="choice-card"
                  :class="form.site_mode === 'existing' ? 'choice-card-active' : ''"
                  @click="form.site_mode = 'existing'"
                >
                  <span class="choice-icon"><i class="fa-solid fa-location-dot"></i></span>
                  <span>
                    <strong>Saved Location</strong>
                    <small>Use an existing property or facility.</small>
                  </span>
                </button>

                <button
                  type="button"
                  class="choice-card"
                  :class="form.site_mode === 'new' ? 'choice-card-active' : ''"
                  @click="startNewSite"
                >
                  <span class="choice-icon"><i class="fa-solid fa-map-location-dot"></i></span>
                  <span>
                    <strong>New Location</strong>
                    <small>Add another location for this customer.</small>
                  </span>
                </button>
              </div>

              <div v-if="form.site_mode === 'existing'" class="mt-4">
                <label for="site-id" class="field-label">Saved Service Location *</label>
                <select id="site-id" v-model.number="form.customer_site_id" class="field-input">
                  <option :value="0">Select service location</option>
                  <option v-for="site in selectedCustomer.sites" :key="site.id" :value="site.id">
                    {{ siteLabel(site) }}
                  </option>
                </select>
              </div>
            </template>

            <p
              v-else-if="form.customer_mode === 'existing' && selectedCustomer"
              class="mt-2 font-nunito text-[12px] text-[#64748B]"
            >
              This customer has no saved service location yet. Enter the first location below.
            </p>
          </div>

          <template v-if="showNewSiteFields">
            <div>
              <label for="site-name" class="field-label">Property / Facility Name</label>
              <input
                id="site-name"
                v-model.trim="form.site.site_name"
                class="field-input"
                type="text"
                maxlength="190"
                placeholder="Residence, Main Office, Warehouse, City Hall..."
              />
            </div>

            <div>
              <label for="property-type" class="field-label">Property Type</label>
              <input
                id="property-type"
                v-model.trim="form.site.property_type"
                class="field-input"
                type="text"
                maxlength="100"
                placeholder="House, office, warehouse, public facility..."
              />
            </div>

            <div class="lg:col-span-2">
              <label for="site-address" class="field-label"
                >Service Address <span class="text-red-500">*</span></label
              >
              <input
                id="site-address"
                v-model.trim="form.site.address_line"
                class="field-input"
                type="text"
                maxlength="255"
                placeholder="House / building number, street, subdivision"
              />
            </div>

            <div>
              <label for="site-barangay" class="field-label">Barangay</label>
              <input
                id="site-barangay"
                v-model.trim="form.site.barangay"
                class="field-input"
                type="text"
                maxlength="120"
              />
            </div>

            <div>
              <label for="site-city" class="field-label">City / Municipality</label>
              <input
                id="site-city"
                v-model.trim="form.site.city"
                class="field-input"
                type="text"
                maxlength="120"
              />
            </div>

            <div>
              <label for="site-province" class="field-label">Province</label>
              <input
                id="site-province"
                v-model.trim="form.site.province"
                class="field-input"
                type="text"
                maxlength="120"
              />
            </div>

            <div>
              <label for="site-contact" class="field-label">On-Site Contact Person</label>
              <input
                id="site-contact"
                v-model.trim="form.site.contact_person"
                class="field-input"
                type="text"
                maxlength="150"
              />
            </div>

            <div>
              <label for="site-contact-number" class="field-label">On-Site Contact Number</label>
              <input
                id="site-contact-number"
                v-model.trim="form.site.contact_number"
                class="field-input"
                type="text"
                maxlength="50"
              />
            </div>
          </template>
        </div>
      </section>

      <!-- Survey and quotation references -->
      <section class="form-card">
        <FormHeading
          number="02"
          icon="fa-regular fa-file-lines"
          title="Survey & Quotation Reference"
          description="For this MVP, APESCON can record the finalized survey and approved quotation details without recreating the full quotation workflow."
        />

        <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
          <div>
            <label for="survey-date" class="field-label">Survey Date</label>
            <input id="survey-date" v-model="form.survey_date" class="field-input" type="date" />
          </div>
          <div>
            <label for="surveyed-by" class="field-label">Surveyed By</label>
            <input
              id="surveyed-by"
              v-model.trim="form.surveyed_by"
              class="field-input"
              type="text"
              maxlength="150"
              placeholder="Staff / surveyor name"
            />
          </div>
          <div>
            <label for="quotation-number" class="field-label">Quotation Number</label>
            <input
              id="quotation-number"
              v-model.trim="form.quotation_number"
              class="field-input"
              type="text"
              maxlength="100"
              placeholder="GPC-2026-0001"
            />
          </div>
          <div>
            <label for="quotation-date" class="field-label">Quotation Date</label>
            <input
              id="quotation-date"
              v-model="form.quotation_date"
              class="field-input"
              type="date"
            />
          </div>
          <div>
            <label for="quotation-valid-until" class="field-label">Valid Until</label>
            <input
              id="quotation-valid-until"
              v-model="form.quotation_valid_until"
              class="field-input"
              type="date"
            />
          </div>
          <div>
            <label for="quotation-amount" class="field-label">Quotation Amount</label>
            <div class="money-field">
              <span>₱</span>
              <input
                id="quotation-amount"
                v-model="form.quotation_amount"
                type="number"
                min="0"
                step="0.01"
                placeholder="0.00"
              />
            </div>
          </div>
        </div>
      </section>

      <!-- Service details -->
      <section class="form-card">
        <FormHeading
          number="03"
          icon="fa-solid fa-spray-can-sparkles"
          title="Service Details"
          description="Capture the important treatment information taken from the finalized APESCON survey, quotation and contract."
        />

        <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
          <div>
            <label for="service-type" class="field-label"
              >Type of Service <span class="text-red-500">*</span></label
            >
            <select id="service-type" v-model.number="form.service_type_id" class="field-input">
              <option :value="0">Select pest-control service</option>
              <option v-for="service in serviceTypes" :key="service.id" :value="service.id">
                {{ service.name }}
              </option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label for="area-coverage" class="field-label">Area Coverage</label>
              <input
                id="area-coverage"
                v-model="form.area_coverage"
                class="field-input"
                type="number"
                min="0"
                step="0.01"
                placeholder="227"
              />
            </div>
            <div>
              <label for="area-unit" class="field-label">Unit</label>
              <select id="area-unit" v-model="form.area_unit" class="field-input">
                <option value="sqm">sqm</option>
                <option value="sqft">sq ft</option>
                <option value="linear_m">linear m</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>

          <div class="lg:col-span-2">
            <label class="field-label">Target Pest(s)</label>
            <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
              <label
                v-for="pest in pestTypes"
                :key="pest.id"
                class="flex cursor-pointer items-center gap-2 rounded-xl border border-[#D8E4ED] bg-white px-3 py-3 font-nunito text-[12px] text-[#526274] transition hover:border-[#025199]/35 hover:bg-[#F8FBFD]"
              >
                <input
                  v-model="form.pest_ids"
                  :value="pest.id"
                  type="checkbox"
                  class="h-4 w-4 rounded border-[#CBD8E2] text-[#025199] focus:ring-[#025199]"
                />
                <span>{{ pest.name }}</span>
              </label>
            </div>
          </div>

          <div>
            <label for="treatment-method" class="field-label">Treatment Method</label>
            <textarea
              id="treatment-method"
              v-model.trim="form.treatment_method"
              class="field-input min-h-28 resize-y"
              maxlength="5000"
              placeholder="Example: soil poisoning, crack & crevice application, sub-slab injection..."
            ></textarea>
          </div>

          <div>
            <label for="chemical-details" class="field-label"
              >Chemical(s) / Technical Specification</label
            >
            <textarea
              id="chemical-details"
              v-model.trim="form.chemical_details"
              class="field-input min-h-28 resize-y"
              maxlength="5000"
              placeholder="Chemical name, dilution, concentration or other contract specification"
            ></textarea>
          </div>

          <div>
            <label for="personnel-count" class="field-label">No. of Personnel</label>
            <input
              id="personnel-count"
              v-model="form.personnel_count"
              class="field-input"
              type="number"
              min="0"
              max="500"
              step="1"
              placeholder="2"
            />
          </div>

          <div>
            <label for="assigned-team" class="field-label">Assigned Team / Technician(s)</label>
            <input
              id="assigned-team"
              v-model.trim="form.assigned_team"
              class="field-input"
              type="text"
              maxlength="190"
              placeholder="Team A / technician names"
            />
          </div>

          <div class="lg:col-span-2">
            <label for="service-scope" class="field-label">Scope of Service</label>
            <textarea
              id="service-scope"
              v-model.trim="form.service_scope"
              class="field-input min-h-32 resize-y"
              maxlength="10000"
              placeholder="Areas covered, service inclusions, customer instructions, treatment scope..."
            ></textarea>
          </div>
        </div>
      </section>

      <!-- Contract and payment -->
      <section class="form-card">
        <FormHeading
          number="04"
          icon="fa-solid fa-file-signature"
          title="Contract & Payment"
          description="The system transaction number is generated automatically; the APESCON contract number remains a separate reference."
        />

        <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
          <div>
            <label for="contract-number" class="field-label">Contract Number</label>
            <input
              id="contract-number"
              v-model.trim="form.contract_number"
              class="field-input"
              type="text"
              maxlength="100"
              placeholder="APESCON contract reference"
            />
          </div>
          <div>
            <label for="contract-date" class="field-label">Contract Date</label>
            <input
              id="contract-date"
              v-model="form.contract_date"
              class="field-input"
              type="date"
            />
          </div>
          <div>
            <label for="contract-amount" class="field-label"
              >Contract Amount <span class="text-red-500">*</span></label
            >
            <div class="money-field">
              <span>₱</span>
              <input
                id="contract-amount"
                v-model="form.contract_amount"
                type="number"
                min="0.01"
                step="0.01"
                placeholder="0.00"
              />
            </div>
          </div>
          <div>
            <label for="contract-start" class="field-label"
              >Contract Start Date <span class="text-red-500">*</span></label
            >
            <input
              id="contract-start"
              v-model="form.contract_start_date"
              class="field-input"
              type="date"
            />
          </div>
          <div>
            <label for="contract-end" class="field-label">Contract End Date</label>
            <input
              id="contract-end"
              v-model="form.contract_end_date"
              class="field-input"
              type="date"
            />
          </div>
          <div>
            <label for="payment-terms" class="field-label"
              >Payment Terms <span class="text-red-500">*</span></label
            >
            <select id="payment-terms" v-model="form.payment_terms" class="field-input">
              <option
                v-for="option in paymentTermOptions"
                :key="option.value"
                :value="option.value"
              >
                {{ option.label }}
              </option>
            </select>
          </div>

          <div>
            <label for="transaction-status" class="field-label"
              >Transaction Status <span class="text-red-500">*</span></label
            >
            <select id="transaction-status" v-model="form.transaction_status" class="field-input">
              <option
                v-for="option in transactionStatusOptions"
                :key="option.value"
                :value="option.value"
              >
                {{ option.label }}
              </option>
            </select>
          </div>
        </div>

        <!-- Initial payment only while creating -->
        <div
          v-if="mode === 'create'"
          class="mt-6 rounded-[20px] border border-[#D8E4ED] bg-[#F8FBFD] p-5"
        >
          <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p
                class="font-montserrat text-[11px] font-semibold uppercase tracking-[0.13em] text-[#163A5F]"
              >
                Initial Payment
              </p>
              <p class="mt-1 font-nunito text-[12px] text-[#64748B]">
                Optional. Leave the amount at zero if the transaction is still unpaid.
              </p>
            </div>
            <span
              class="rounded-full border px-3 py-1.5 font-montserrat text-[9px] font-semibold uppercase tracking-[0.12em]"
              :class="initialPaymentBadgeClass"
            >
              {{ initialPaymentStatusLabel }}
            </span>
          </div>

          <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div>
              <label for="initial-payment" class="field-label">Amount Paid</label>
              <div class="money-field bg-white">
                <span>₱</span>
                <input
                  id="initial-payment"
                  v-model="form.initial_payment.amount"
                  type="number"
                  min="0"
                  step="0.01"
                  placeholder="0.00"
                />
              </div>
            </div>
            <div>
              <label for="initial-payment-date" class="field-label">Payment Date</label>
              <input
                id="initial-payment-date"
                v-model="form.initial_payment.payment_date"
                class="field-input bg-white"
                type="date"
              />
            </div>
            <div>
              <label for="initial-payment-method" class="field-label">Payment Method</label>
              <select
                id="initial-payment-method"
                v-model="form.initial_payment.payment_method"
                class="field-input bg-white"
              >
                <option
                  v-for="option in paymentMethodOptions"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
            </div>
            <div>
              <label for="initial-payment-ref" class="field-label">Reference / Receipt No.</label>
              <input
                id="initial-payment-ref"
                v-model.trim="form.initial_payment.reference_number"
                class="field-input bg-white"
                type="text"
                maxlength="150"
                placeholder="Optional"
              />
            </div>
          </div>
        </div>
      </section>

      <!-- Schedule and warranty -->
      <section class="form-card">
        <FormHeading
          number="05"
          icon="fa-regular fa-calendar-check"
          title="Service Schedule & Warranty"
          description="These records are saved to service_visits now, so the future calendar module can display them without redesigning the transaction database."
        />

        <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
          <div>
            <label for="initial-service-date" class="field-label"
              >Initial Service Date <span class="text-red-500">*</span></label
            >
            <input
              id="initial-service-date"
              v-model="form.initial_service_date"
              class="field-input"
              type="date"
            />
          </div>
          <div>
            <label for="initial-service-time" class="field-label">Preferred Time</label>
            <input
              id="initial-service-time"
              v-model="form.initial_service_time"
              class="field-input"
              type="time"
            />
          </div>
          <div>
            <label for="service-supervisor" class="field-label">Service Supervisor</label>
            <input
              id="service-supervisor"
              v-model.trim="form.service_supervisor"
              class="field-input"
              type="text"
              maxlength="150"
            />
          </div>
          <div>
            <label for="service-frequency" class="field-label"
              >Service Frequency <span class="text-red-500">*</span></label
            >
            <select
              id="service-frequency"
              v-model="form.service_frequency"
              class="field-input"
              @change="onFrequencyChange"
            >
              <option v-for="option in frequencyOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </div>
          <div>
            <label for="follow-up-visits" class="field-label">No. of Follow-Up Visits</label>
            <input
              id="follow-up-visits"
              v-model="form.follow_up_visits"
              class="field-input"
              type="number"
              min="0"
              max="60"
              step="1"
              :disabled="form.service_frequency === 'one_time'"
            />
            <p class="mt-1.5 font-nunito text-[11px] text-[#94A3B8]">
              This is separate from the initial treatment.
            </p>
          </div>
          <div v-if="form.service_frequency === 'custom'">
            <label for="custom-interval" class="field-label">Custom Interval (Days)</label>
            <input
              id="custom-interval"
              v-model="form.custom_interval_days"
              class="field-input"
              type="number"
              min="1"
              max="3650"
              step="1"
            />
          </div>
        </div>

        <!-- Schedule preview -->
        <div class="mt-6 overflow-hidden rounded-[20px] border border-[#D8E4ED] bg-[#F8FBFD]">
          <div class="flex items-center justify-between border-b border-[#D8E4ED] px-5 py-4">
            <div>
              <p
                class="font-montserrat text-[11px] font-semibold uppercase tracking-[0.13em] text-[#163A5F]"
              >
                Generated Schedule Preview
              </p>
              <p class="mt-1 font-nunito text-[11px] text-[#64748B]">
                Dates can later be individually rescheduled without deleting the original
                transaction.
              </p>
            </div>
            <i class="fa-regular fa-calendar text-[#025199]"></i>
          </div>

          <div v-if="schedulePreview.length" class="divide-y divide-[#E6EEF4]">
            <div
              v-for="visit in schedulePreview"
              :key="visit.number"
              class="flex items-center gap-4 px-5 py-3.5"
            >
              <span
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#025199]/8 font-montserrat text-[10px] font-bold text-[#025199]"
              >
                {{ visit.number }}
              </span>
              <div class="min-w-0 flex-1">
                <p class="truncate font-montserrat text-[11px] font-semibold text-[#163A5F]">
                  {{ visit.title }}
                </p>
                <p class="mt-0.5 font-nunito text-[11px] text-[#64748B]">
                  {{ formatDateLong(visit.date) }}
                </p>
              </div>
              <span
                class="hidden rounded-full bg-white px-3 py-1 font-montserrat text-[8px] font-semibold uppercase tracking-widest text-[#64748B] sm:inline-flex"
              >
                {{ visit.number === 1 ? 'Initial' : 'Follow-Up' }}
              </span>
            </div>
          </div>
          <div v-else class="px-5 py-8 text-center font-nunito text-[12px] text-[#94A3B8]">
            Select an initial service date to preview the schedule.
          </div>
        </div>

        <!-- Warranty -->
        <div class="mt-6 border-t border-[#E6EEF4] pt-6">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p
                class="font-montserrat text-[11px] font-semibold uppercase tracking-[0.13em] text-[#163A5F]"
              >
                Warranty Coverage
              </p>
              <p class="mt-1 font-nunito text-[12px] text-[#64748B]">
                Use the actual dates stated in the signed contract; the system does not assume every
                service has a one-year warranty.
              </p>
            </div>
            <label
              class="inline-flex cursor-pointer items-center gap-3 rounded-full border border-[#D8E4ED] bg-[#F8FBFD] px-4 py-2.5"
            >
              <input
                v-model="form.warranty_included"
                type="checkbox"
                class="h-4 w-4 rounded border-[#CBD8E2] text-[#025199] focus:ring-[#025199]"
              />
              <span
                class="font-montserrat text-[9px] font-semibold uppercase tracking-[0.12em] text-[#526274]"
                >Warranty Included</span
              >
            </label>
          </div>

          <div v-if="form.warranty_included" class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
              <label for="warranty-start" class="field-label"
                >Warranty Start <span class="text-red-500">*</span></label
              >
              <input
                id="warranty-start"
                v-model="form.warranty_start_date"
                class="field-input"
                type="date"
              />
            </div>
            <div>
              <label for="warranty-end" class="field-label"
                >Warranty End <span class="text-red-500">*</span></label
              >
              <input
                id="warranty-end"
                v-model="form.warranty_end_date"
                class="field-input"
                type="date"
              />
            </div>
            <div class="md:col-span-2">
              <label for="warranty-notes" class="field-label">Warranty Conditions / Notes</label>
              <textarea
                id="warranty-notes"
                v-model.trim="form.warranty_notes"
                class="field-input min-h-24 resize-y"
                maxlength="5000"
                placeholder="Coverage, exclusions, callback conditions or other signed contract terms"
              ></textarea>
            </div>
          </div>
        </div>
      </section>

      <!-- Government fields -->
      <section v-if="effectiveCustomerType === 'government'" class="form-card">
        <FormHeading
          number="06"
          icon="fa-solid fa-landmark"
          title="Government References"
          description="Basic government-contract readiness for Phase 1. This does not replace PhilGEPS or a future bidding module."
        />

        <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
          <div class="md:col-span-2 xl:col-span-3">
            <label for="government-title" class="field-label">Project / Contract Title</label>
            <input
              id="government-title"
              v-model.trim="form.government_project_title"
              class="field-input"
              type="text"
              maxlength="255"
            />
          </div>
          <div>
            <label for="government-reference" class="field-label">Procurement Reference</label>
            <input
              id="government-reference"
              v-model.trim="form.government_reference"
              class="field-input"
              type="text"
              maxlength="150"
            />
          </div>
          <div>
            <label for="philgeps-reference" class="field-label">PhilGEPS Reference</label>
            <input
              id="philgeps-reference"
              v-model.trim="form.philgeps_reference"
              class="field-input"
              type="text"
              maxlength="150"
            />
          </div>
          <div>
            <label for="po-number" class="field-label">Purchase Order No.</label>
            <input
              id="po-number"
              v-model.trim="form.purchase_order_number"
              class="field-input"
              type="text"
              maxlength="150"
            />
          </div>
          <div>
            <label for="ntp-date" class="field-label">Notice to Proceed Date</label>
            <input
              id="ntp-date"
              v-model="form.notice_to_proceed_date"
              class="field-input"
              type="date"
            />
          </div>
          <div class="md:col-span-2">
            <label for="government-representative" class="field-label"
              >Government Representative / Contact</label
            >
            <input
              id="government-representative"
              v-model.trim="form.government_representative"
              class="field-input"
              type="text"
              maxlength="190"
            />
          </div>
        </div>
      </section>

      <!-- Notes & Review -->
      <section class="form-card">
        <FormHeading
          :number="effectiveCustomerType === 'government' ? '07' : '06'"
          icon="fa-regular fa-note-sticky"
          title="Internal Notes & Review"
          description="Add internal remarks, then review the transaction summary before saving."
        />

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-5">
          <div class="lg:col-span-3">
            <label for="transaction-notes" class="field-label">Internal Notes</label>
            <textarea
              id="transaction-notes"
              v-model.trim="form.notes"
              class="field-input min-h-40 resize-y"
              maxlength="10000"
              placeholder="Internal reminders, special access instructions, important client arrangements..."
            ></textarea>
          </div>

          <aside
            class="rounded-[20px] bg-linear-to-br from-[#062B4C] via-[#025199] to-[#073D68] p-5 text-white shadow-[0_18px_42px_rgba(5,35,61,0.16)] lg:col-span-2"
          >
            <p
              class="font-montserrat text-[9px] font-semibold uppercase tracking-[0.18em] text-[#F7B267]"
            >
              Transaction Review
            </p>
            <dl class="mt-5 space-y-3">
              <div class="review-row">
                <dt>Customer</dt>
                <dd>{{ reviewCustomerName }}</dd>
              </div>
              <div class="review-row">
                <dt>Service</dt>
                <dd>{{ selectedService?.name || 'Not selected' }}</dd>
              </div>
              <div class="review-row">
                <dt>Contract</dt>
                <dd>{{ formatMoney(form.contract_amount) }}</dd>
              </div>
              <div class="review-row">
                <dt>Initial Service</dt>
                <dd>
                  {{
                    form.initial_service_date
                      ? formatDateLong(form.initial_service_date)
                      : 'Not set'
                  }}
                </dd>
              </div>
              <div class="review-row">
                <dt>Follow-Ups</dt>
                <dd>{{ Number(form.follow_up_visits || 0) }}</dd>
              </div>
              <div class="review-row">
                <dt>Warranty</dt>
                <dd>
                  {{
                    form.warranty_included
                      ? `${form.warranty_start_date || '—'} to ${form.warranty_end_date || '—'}`
                      : 'Not included'
                  }}
                </dd>
              </div>
            </dl>
          </aside>
        </div>
      </section>

      <!-- Actions -->
      <div
        class="sticky bottom-4 z-20 rounded-[20px] border border-[#D8E4ED] bg-white/95 p-4 shadow-[0_18px_50px_rgba(5,35,61,0.13)] backdrop-blur-xl sm:p-5"
      >
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <p class="font-nunito text-[11px] leading-5 text-[#64748B]">
            Important changes remain traceable through the transaction activity history.
          </p>
          <div class="flex flex-col-reverse gap-3 sm:flex-row">
            <button
              type="button"
              class="secondary-button"
              :disabled="saving"
              @click="$emit('cancel')"
            >
              Cancel
            </button>
            <button type="submit" class="primary-button" :disabled="saving">
              <i v-if="saving" class="fa-solid fa-spinner animate-spin"></i>
              <i v-else class="fa-solid fa-floppy-disk"></i>
              {{ saving ? 'Saving...' : mode === 'edit' ? 'Save Changes' : 'Create Transaction' }}
            </button>
          </div>
        </div>
      </div>
    </template>
  </form>
</template>

<script setup>
import { computed, defineComponent, h, onMounted, reactive, ref, watch } from 'vue'
import { transactionService } from '@/services/transactionService'

const props = defineProps({
  mode: {
    type: String,
    default: 'create',
  },
  initialData: {
    type: Object,
    default: null,
  },
  saving: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['submit', 'cancel'])

const FormHeading = defineComponent({
  props: {
    number: String,
    icon: String,
    title: String,
    description: String,
  },
  setup(componentProps) {
    return () =>
      h('div', { class: 'flex items-start gap-4' }, [
        h(
          'span',
          {
            class:
              'flex h-11 w-11 shrink-0 items-center justify-center rounded-[14px] bg-[#025199] text-white shadow-[0_10px_22px_rgba(2,81,153,0.20)]',
          },
          [h('i', { class: `${componentProps.icon} text-[14px]` })],
        ),
        h('div', { class: 'min-w-0 flex-1' }, [
          h('div', { class: 'flex flex-wrap items-center gap-2' }, [
            h(
              'span',
              {
                class:
                  'font-montserrat text-[9px] font-semibold uppercase tracking-[0.16em] text-[#F27C29]',
              },
              `Section ${componentProps.number}`,
            ),
          ]),
          h(
            'h2',
            { class: 'mt-1 font-montserrat text-[18px] font-bold text-[#163A5F] sm:text-[20px]' },
            componentProps.title,
          ),
          h(
            'p',
            { class: 'mt-1.5 max-w-3xl font-nunito text-[12px] leading-5 text-[#64748B]' },
            componentProps.description,
          ),
        ]),
      ])
  },
})

const serviceTypes = ref([])
const pestTypes = ref([])
const customers = ref([])
const isLoadingReference = ref(true)
const validationErrors = ref([])

const emptyCustomer = () => ({
  customer_type: 'residential',
  display_name: '',
  contact_person: '',
  contact_number: '',
  email_address: '',
  billing_address: '',
  tin: '',
})

const emptySite = () => ({
  site_name: '',
  address_line: '',
  barangay: '',
  city: '',
  province: '',
  property_type: '',
  contact_person: '',
  contact_number: '',
  notes: '',
})

const form = reactive({
  customer_mode: 'new',
  customer_id: 0,
  site_mode: 'new',
  customer_site_id: 0,
  customer: emptyCustomer(),
  site: emptySite(),

  survey_date: '',
  surveyed_by: '',
  quotation_number: '',
  quotation_date: '',
  quotation_valid_until: '',
  quotation_amount: '',

  service_type_id: 0,
  pest_ids: [],
  treatment_method: '',
  chemical_details: '',
  area_coverage: '',
  area_unit: 'sqm',
  personnel_count: '',
  service_scope: '',

  contract_number: '',
  contract_date: '',
  contract_amount: '',
  contract_start_date: '',
  contract_end_date: '',
  payment_terms: 'full_before_service',
  transaction_status: 'active',

  initial_payment: {
    amount: '0.00',
    payment_date: '',
    payment_method: 'cash',
    reference_number: '',
    remarks: '',
  },

  initial_service_date: '',
  initial_service_time: '',
  assigned_team: '',
  service_supervisor: '',
  service_frequency: 'one_time',
  follow_up_visits: 0,
  custom_interval_days: '',

  warranty_included: false,
  warranty_start_date: '',
  warranty_end_date: '',
  warranty_notes: '',

  government_project_title: '',
  government_reference: '',
  philgeps_reference: '',
  purchase_order_number: '',
  notice_to_proceed_date: '',
  government_representative: '',

  notes: '',
})

const customerTypeOptions = [
  { value: 'residential', label: 'Residential' },
  { value: 'commercial', label: 'Commercial' },
  { value: 'industrial', label: 'Industrial' },
  { value: 'institutional', label: 'Institutional' },
  { value: 'government', label: 'Government' },
  { value: 'agricultural', label: 'Agricultural' },
]

const paymentTermOptions = [
  { value: 'full_before_service', label: 'Full Payment Before Service' },
  { value: 'full_after_service', label: 'Full Payment After Service' },
  { value: 'partial_installment', label: 'Partial / Installment' },
  { value: 'per_visit', label: 'Per Service Visit' },
  { value: 'government_processing', label: 'Government Processing' },
  { value: 'other', label: 'Other' },
]

const paymentMethodOptions = [
  { value: 'cash', label: 'Cash' },
  { value: 'bank_transfer', label: 'Bank Transfer' },
  { value: 'gcash', label: 'GCash' },
  { value: 'check', label: 'Check' },
  { value: 'government_disbursement', label: 'Government Disbursement' },
  { value: 'other', label: 'Other' },
]

const frequencyOptions = [
  { value: 'one_time', label: 'One-Time' },
  { value: 'weekly', label: 'Weekly' },
  { value: 'monthly', label: 'Monthly' },
  { value: 'quarterly', label: 'Quarterly' },
  { value: 'semi_annual', label: 'Semi-Annual' },
  { value: 'annual', label: 'Annual' },
  { value: 'custom', label: 'Custom Interval' },
]

const transactionStatusOptions = computed(() => {
  const base = [
    { value: 'draft', label: 'Draft' },
    { value: 'active', label: 'Active' },
  ]

  if (props.mode === 'edit') {
    base.push({ value: 'completed', label: 'Completed' })
  }

  return base
})

const selectedCustomer = computed(
  () => customers.value.find((customer) => customer.id === Number(form.customer_id)) || null,
)

const selectedService = computed(
  () => serviceTypes.value.find((service) => service.id === Number(form.service_type_id)) || null,
)

const effectiveCustomerType = computed(() => {
  if (form.customer_mode === 'existing') {
    return selectedCustomer.value?.customer_type || ''
  }

  return form.customer.customer_type
})

const showNewSiteFields = computed(() => {
  if (form.customer_mode === 'new') return true
  if (!selectedCustomer.value) return false
  if (!selectedCustomer.value.sites?.length) return true
  return form.site_mode === 'new'
})

const reviewCustomerName = computed(() => {
  if (form.customer_mode === 'existing') {
    return selectedCustomer.value?.display_name || 'Not selected'
  }

  return form.customer.display_name || 'Not entered'
})

const initialPaymentAmount = computed(() => Math.max(0, Number(form.initial_payment.amount || 0)))
const contractAmountNumber = computed(() => Math.max(0, Number(form.contract_amount || 0)))

const initialPaymentStatusLabel = computed(() => {
  if (initialPaymentAmount.value <= 0) return 'Unpaid'
  if (initialPaymentAmount.value + 0.00001 < contractAmountNumber.value) return 'Partial'
  return 'Paid'
})

const initialPaymentBadgeClass = computed(() => {
  if (initialPaymentAmount.value <= 0) return 'border-[#F0D2D5] bg-[#FFF5F5] text-[#B4232D]'
  if (initialPaymentAmount.value + 0.00001 < contractAmountNumber.value) {
    return 'border-[#F1DDC9] bg-[#FFF8F1] text-[#9A5525]'
  }
  return 'border-[#B9DFC9] bg-[#F0FAF4] text-[#267149]'
})

const parseLocalDate = (value) => {
  if (!value) return null
  const [year, month, day] = value.split('-').map(Number)
  if (!year || !month || !day) return null
  return new Date(year, month - 1, day, 12, 0, 0)
}

const dateToYmd = (date) => {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const addMonthsClamped = (date, months) => {
  const day = date.getDate()
  const target = new Date(date.getFullYear(), date.getMonth() + months, 1, 12, 0, 0)
  const lastDay = new Date(target.getFullYear(), target.getMonth() + 1, 0, 12, 0, 0).getDate()
  target.setDate(Math.min(day, lastDay))
  return target
}

const addDays = (date, days) => {
  const target = new Date(date)
  target.setDate(target.getDate() + days)
  return target
}

const followUpDate = (base, sequence) => {
  switch (form.service_frequency) {
    case 'weekly':
      return addDays(base, sequence * 7)
    case 'monthly':
      return addMonthsClamped(base, sequence)
    case 'quarterly':
      return addMonthsClamped(base, sequence * 3)
    case 'semi_annual':
      return addMonthsClamped(base, sequence * 6)
    case 'annual':
      return addMonthsClamped(base, sequence * 12)
    case 'custom':
      return addDays(base, sequence * Math.max(1, Number(form.custom_interval_days || 1)))
    default:
      return base
  }
}

const followUpTitle = (sequence) => {
  const labels = {
    weekly: 'Weekly Follow-Up',
    monthly: 'Monthly Follow-Up',
    quarterly: 'Quarterly Visit',
    semi_annual: 'Semi-Annual Visit',
    annual: 'Annual Visit',
    custom: 'Scheduled Follow-Up',
  }

  return `${labels[form.service_frequency] || 'Follow-Up Visit'} #${sequence}`
}

const schedulePreview = computed(() => {
  const base = parseLocalDate(form.initial_service_date)
  if (!base) return []

  const visits = [
    {
      number: 1,
      title: 'Initial Service',
      date: dateToYmd(base),
    },
  ]

  if (form.service_frequency === 'one_time') return visits

  const followUps = Math.min(60, Math.max(0, Number(form.follow_up_visits || 0)))

  for (let sequence = 1; sequence <= followUps; sequence += 1) {
    visits.push({
      number: sequence + 1,
      title: followUpTitle(sequence),
      date: dateToYmd(followUpDate(base, sequence)),
    })
  }

  return visits
})

const customerTypeLabel = (value) =>
  customerTypeOptions.find((option) => option.value === value)?.label || value || 'Customer'

const siteLabel = (site) => {
  const name = site.site_name ? `${site.site_name} — ` : ''
  const locality = [site.city, site.province].filter(Boolean).join(', ')
  return `${name}${site.address_line}${locality ? `, ${locality}` : ''}`
}

const formatMoney = (value) => {
  const amount = Number(value || 0)
  return new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
    minimumFractionDigits: 2,
  }).format(Number.isFinite(amount) ? amount : 0)
}

const formatDateLong = (value) => {
  const date = parseLocalDate(value)
  if (!date) return '—'

  return new Intl.DateTimeFormat('en-PH', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  }).format(date)
}

const setCustomerMode = (mode) => {
  form.customer_mode = mode
  validationErrors.value = []

  if (mode === 'new') {
    form.customer_id = 0
    form.customer_site_id = 0
    form.site_mode = 'new'
  }
}

const onCustomerChange = () => {
  form.customer_site_id = 0
  form.site_mode = selectedCustomer.value?.sites?.length ? 'existing' : 'new'
  Object.assign(form.site, emptySite())
}

const startNewSite = () => {
  form.site_mode = 'new'
  form.customer_site_id = 0
}

const onFrequencyChange = () => {
  if (form.service_frequency === 'one_time') {
    form.follow_up_visits = 0
    form.custom_interval_days = ''
  }
}

const resetGovernmentFieldsIfNeeded = () => {
  if (effectiveCustomerType.value === 'government') return

  form.government_project_title = ''
  form.government_reference = ''
  form.philgeps_reference = ''
  form.purchase_order_number = ''
  form.notice_to_proceed_date = ''
  form.government_representative = ''
}

watch(effectiveCustomerType, resetGovernmentFieldsIfNeeded)

const validate = () => {
  const errors = []

  if (form.customer_mode === 'existing') {
    if (!Number(form.customer_id)) errors.push('Select an existing customer.')

    if (showNewSiteFields.value) {
      if (!form.site.address_line.trim()) errors.push('Enter the service address.')
    } else if (!Number(form.customer_site_id)) {
      errors.push('Select a saved service location.')
    }
  } else {
    if (!form.customer.display_name.trim()) errors.push('Enter the customer or organization name.')
    if (!form.site.address_line.trim()) errors.push('Enter the service address.')
  }

  if (!Number(form.service_type_id)) errors.push('Select the type of pest-control service.')
  if (contractAmountNumber.value <= 0) errors.push('Contract amount must be greater than zero.')
  if (!form.contract_start_date) errors.push('Contract start date is required.')
  if (!form.initial_service_date) errors.push('Initial service date is required.')

  if (form.contract_end_date && form.contract_end_date < form.contract_start_date) {
    errors.push('Contract end date cannot be earlier than the contract start date.')
  }

  if (form.service_frequency !== 'one_time' && Number(form.follow_up_visits || 0) < 0) {
    errors.push('Follow-up visit count is invalid.')
  }

  if (
    form.service_frequency === 'custom' &&
    Number(form.follow_up_visits || 0) > 0 &&
    Number(form.custom_interval_days || 0) <= 0
  ) {
    errors.push('Enter the number of days between custom follow-up visits.')
  }

  if (form.warranty_included) {
    if (!form.warranty_start_date || !form.warranty_end_date) {
      errors.push('Warranty start and end dates are required.')
    } else if (form.warranty_end_date < form.warranty_start_date) {
      errors.push('Warranty end date cannot be earlier than the warranty start date.')
    }
  }

  if (props.mode === 'create' && initialPaymentAmount.value > 0) {
    if (initialPaymentAmount.value > contractAmountNumber.value + 0.00001) {
      errors.push('Initial payment cannot exceed the contract amount.')
    }
    if (!form.initial_payment.payment_date) errors.push('Enter the initial payment date.')
    if (!form.initial_payment.payment_method) errors.push('Select the initial payment method.')
  }

  validationErrors.value = errors
  return errors.length === 0
}

const normalizeNullable = (value) => {
  const normalized = String(value ?? '').trim()
  return normalized === '' ? null : normalized
}

const buildPayload = () => ({
  customer_mode: form.customer_mode,
  customer_id: Number(form.customer_id || 0),
  site_mode: showNewSiteFields.value ? 'new' : form.site_mode,
  customer_site_id: Number(form.customer_site_id || 0),
  customer: { ...form.customer },
  site: { ...form.site },

  survey_date: normalizeNullable(form.survey_date),
  surveyed_by: normalizeNullable(form.surveyed_by),
  quotation_number: normalizeNullable(form.quotation_number),
  quotation_date: normalizeNullable(form.quotation_date),
  quotation_valid_until: normalizeNullable(form.quotation_valid_until),
  quotation_amount: form.quotation_amount === '' ? null : Number(form.quotation_amount),

  service_type_id: Number(form.service_type_id || 0),
  pest_ids: form.pest_ids.map(Number),
  treatment_method: normalizeNullable(form.treatment_method),
  chemical_details: normalizeNullable(form.chemical_details),
  area_coverage: form.area_coverage === '' ? null : Number(form.area_coverage),
  area_unit: normalizeNullable(form.area_unit),
  personnel_count: form.personnel_count === '' ? null : Number(form.personnel_count),
  service_scope: normalizeNullable(form.service_scope),

  contract_number: normalizeNullable(form.contract_number),
  contract_date: normalizeNullable(form.contract_date),
  contract_amount: Number(form.contract_amount || 0),
  contract_start_date: form.contract_start_date,
  contract_end_date: normalizeNullable(form.contract_end_date),
  payment_terms: form.payment_terms,
  transaction_status: form.transaction_status,

  initial_payment:
    props.mode === 'create'
      ? {
          amount: initialPaymentAmount.value,
          payment_date: normalizeNullable(form.initial_payment.payment_date),
          payment_method: form.initial_payment.payment_method,
          reference_number: normalizeNullable(form.initial_payment.reference_number),
          remarks: normalizeNullable(form.initial_payment.remarks),
        }
      : undefined,

  initial_service_date: form.initial_service_date,
  initial_service_time: normalizeNullable(form.initial_service_time),
  assigned_team: normalizeNullable(form.assigned_team),
  service_supervisor: normalizeNullable(form.service_supervisor),
  service_frequency: form.service_frequency,
  follow_up_visits: form.service_frequency === 'one_time' ? 0 : Number(form.follow_up_visits || 0),
  custom_interval_days:
    form.service_frequency === 'custom' ? Number(form.custom_interval_days || 0) : null,

  warranty_included: Boolean(form.warranty_included),
  warranty_start_date: form.warranty_included ? normalizeNullable(form.warranty_start_date) : null,
  warranty_end_date: form.warranty_included ? normalizeNullable(form.warranty_end_date) : null,
  warranty_notes: form.warranty_included ? normalizeNullable(form.warranty_notes) : null,

  government_project_title:
    effectiveCustomerType.value === 'government'
      ? normalizeNullable(form.government_project_title)
      : null,
  government_reference:
    effectiveCustomerType.value === 'government'
      ? normalizeNullable(form.government_reference)
      : null,
  philgeps_reference:
    effectiveCustomerType.value === 'government'
      ? normalizeNullable(form.philgeps_reference)
      : null,
  purchase_order_number:
    effectiveCustomerType.value === 'government'
      ? normalizeNullable(form.purchase_order_number)
      : null,
  notice_to_proceed_date:
    effectiveCustomerType.value === 'government'
      ? normalizeNullable(form.notice_to_proceed_date)
      : null,
  government_representative:
    effectiveCustomerType.value === 'government'
      ? normalizeNullable(form.government_representative)
      : null,

  notes: normalizeNullable(form.notes),
})

const submitForm = () => {
  if (!validate()) {
    window.scrollTo({ top: 0, behavior: 'smooth' })
    return
  }

  emit('submit', buildPayload())
}

const applyInitialData = () => {
  if (!props.initialData) return

  const tx = props.initialData.transaction || props.initialData
  const pestIds = props.initialData.pest_ids || []

  form.customer_mode = 'existing'
  form.customer_id = Number(tx.customer_id || 0)
  form.site_mode = 'existing'
  form.customer_site_id = Number(tx.customer_site_id || 0)

  form.survey_date = tx.survey_date || ''
  form.surveyed_by = tx.surveyed_by || ''
  form.quotation_number = tx.quotation_number || ''
  form.quotation_date = tx.quotation_date || ''
  form.quotation_valid_until = tx.quotation_valid_until || ''
  form.quotation_amount = tx.quotation_amount ?? ''

  form.service_type_id = Number(tx.service_type_id || 0)
  form.pest_ids = pestIds.map(Number)
  form.treatment_method = tx.treatment_method || ''
  form.chemical_details = tx.chemical_details || ''
  form.area_coverage = tx.area_coverage ?? ''
  form.area_unit = tx.area_unit || 'sqm'
  form.personnel_count = tx.personnel_count ?? ''
  form.service_scope = tx.service_scope || ''

  form.contract_number = tx.contract_number || ''
  form.contract_date = tx.contract_date || ''
  form.contract_amount = tx.contract_amount ?? ''
  form.contract_start_date = tx.contract_start_date || ''
  form.contract_end_date = tx.contract_end_date || ''
  form.payment_terms = tx.payment_terms || 'full_before_service'
  form.transaction_status =
    tx.transaction_status === 'cancelled' ? 'active' : tx.transaction_status || 'active'

  form.initial_service_date = tx.initial_service_date || ''
  form.initial_service_time = tx.initial_service_time
    ? String(tx.initial_service_time).slice(0, 5)
    : ''
  form.assigned_team = tx.assigned_team || ''
  form.service_supervisor = tx.service_supervisor || ''
  form.service_frequency = tx.service_frequency || 'one_time'
  form.follow_up_visits = Number(tx.follow_up_visits || 0)
  form.custom_interval_days = tx.custom_interval_days ?? ''

  form.warranty_included = Boolean(tx.warranty_included)
  form.warranty_start_date = tx.warranty_start_date || ''
  form.warranty_end_date = tx.warranty_end_date || ''
  form.warranty_notes = tx.warranty_notes || ''

  form.government_project_title = tx.government_project_title || ''
  form.government_reference = tx.government_reference || ''
  form.philgeps_reference = tx.philgeps_reference || ''
  form.purchase_order_number = tx.purchase_order_number || ''
  form.notice_to_proceed_date = tx.notice_to_proceed_date || ''
  form.government_representative = tx.government_representative || ''
  form.notes = tx.notes || ''
}

onMounted(async () => {
  isLoadingReference.value = true

  try {
    const [referenceData, customerData] = await Promise.all([
      transactionService.referenceData(),
      transactionService.customers({ limit: 500 }),
    ])

    serviceTypes.value = referenceData.service_types || []
    pestTypes.value = referenceData.pest_types || []
    customers.value = customerData.customers || []

    applyInitialData()
  } catch (error) {
    console.error('Unable to load transaction form reference data:', error)
    validationMessage.value =
      error?.message ||
      'Unable to load the service and customer reference data. Please refresh the page.'
  } finally {
    isLoadingReference.value = false
  }
})
</script>

<style scoped>
.form-card {
  border: 1px solid #d8e4ed;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.98);
  padding: 1.25rem;
  box-shadow: 0 12px 32px rgba(2, 81, 153, 0.05);
}

.field-label {
  display: block;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.625rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #163a5f;
}

.field-input {
  margin-top: 0.5rem;
  width: 100%;
  min-height: 2.875rem;
  border: 1px solid #d8e4ed;
  border-radius: 0.875rem;
  background: #fff;
  padding: 0.75rem 0.875rem;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 0.875rem;
  color: #334155;
  outline: none;
  transition:
    border-color 180ms ease,
    box-shadow 180ms ease,
    transform 180ms ease;
}

.field-input:focus {
  border-color: #025199;
  box-shadow: 0 0 0 4px rgba(2, 81, 153, 0.08);
}

.field-input:disabled {
  cursor: not-allowed;
  background: #f1f5f9;
  color: #94a3b8;
}

.choice-card {
  display: flex;
  min-height: 4.5rem;
  width: 100%;
  align-items: center;
  gap: 0.875rem;
  border: 1px solid #d8e4ed;
  border-radius: 1rem;
  background: #fff;
  padding: 0.875rem;
  text-align: left;
  transition:
    border-color 180ms ease,
    background-color 180ms ease,
    box-shadow 180ms ease;
}

.choice-card:hover {
  border-color: rgba(2, 81, 153, 0.32);
  background: #f8fbfd;
}

.choice-card-active {
  border-color: rgba(2, 81, 153, 0.48);
  background: #f2f7fb;
  box-shadow: 0 0 0 3px rgba(2, 81, 153, 0.06);
}

.choice-card strong {
  display: block;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.6875rem;
  font-weight: 700;
  color: #163a5f;
}

.choice-card small {
  display: block;
  margin-top: 0.2rem;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 0.6875rem;
  line-height: 1.1rem;
  color: #64748b;
}

.choice-icon {
  display: flex;
  height: 2.25rem;
  width: 2.25rem;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  border-radius: 0.75rem;
  background: rgba(2, 81, 153, 0.08);
  color: #025199;
  font-size: 0.75rem;
}

.choice-card-active .choice-icon {
  background: #025199;
  color: #fff;
}

.money-field {
  margin-top: 0.5rem;
  display: flex;
  min-height: 2.875rem;
  align-items: center;
  overflow: hidden;
  border: 1px solid #d8e4ed;
  border-radius: 0.875rem;
  background: #fff;
  transition:
    border-color 180ms ease,
    box-shadow 180ms ease;
}

.money-field:focus-within {
  border-color: #025199;
  box-shadow: 0 0 0 4px rgba(2, 81, 153, 0.08);
}

.money-field > span {
  display: flex;
  align-self: stretch;
  align-items: center;
  border-right: 1px solid #e2e8f0;
  background: #f8fbfd;
  padding: 0 0.875rem;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.75rem;
  font-weight: 700;
  color: #025199;
}

.money-field input {
  min-width: 0;
  flex: 1;
  background: transparent;
  padding: 0.75rem 0.875rem;
  font-family: 'Nunito Sans', sans-serif;
  font-size: 0.875rem;
  color: #334155;
  outline: none;
}

.review-row {
  display: grid;
  grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
  gap: 0.75rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.12);
  padding-bottom: 0.75rem;
}

.review-row:last-child {
  border-bottom: 0;
  padding-bottom: 0;
}

.review-row dt {
  font-family: 'Nunito Sans', sans-serif;
  font-size: 0.6875rem;
  color: rgba(255, 255, 255, 0.56);
}

.review-row dd {
  text-align: right;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.6875rem;
  font-weight: 600;
  color: #fff;
}

.primary-button,
.secondary-button {
  display: inline-flex;
  min-height: 2.875rem;
  align-items: center;
  justify-content: center;
  gap: 0.625rem;
  border-radius: 0.875rem;
  padding: 0.75rem 1.125rem;
  font-family: 'Montserrat', sans-serif;
  font-size: 0.625rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  transition:
    transform 180ms ease,
    background-color 180ms ease,
    border-color 180ms ease;
}

.primary-button {
  border: 1px solid #025199;
  background: #025199;
  color: #fff;
  box-shadow: 0 10px 24px rgba(2, 81, 153, 0.2);
}

.primary-button:hover:not(:disabled) {
  background: #063f73;
  transform: translateY(-1px);
}

.secondary-button {
  border: 1px solid #cbd8e2;
  background: #fff;
  color: #64748b;
}

.secondary-button:hover:not(:disabled) {
  border-color: #025199;
  color: #025199;
}

.primary-button:disabled,
.secondary-button:disabled {
  cursor: not-allowed;
  opacity: 0.55;
}

@media (min-width: 640px) {
  .form-card {
    padding: 1.5rem;
  }
}
</style>
