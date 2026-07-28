<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import PublicLayout from '@/components/PublicLayout.svelte';
    import { MapPin, Mail, Phone, Clock, Send } from 'lucide-svelte';

    let {
        departments = [],
        navigationLinks = [],
    }: {
        departments?: any[];
        navigationLinks?: any[];
    } = $props();

    let name = $state('');
    let email = $state('');
    let message = $state('');
    let submitted = $state(false);

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        // Simulate local submission
        submitted = true;
        name = '';
        email = '';
        message = '';
        setTimeout(() => {
            submitted = false;
        }, 5000);
    }
</script>

<AppHead title="Contact Us - San Isidro College" />

<PublicLayout {navigationLinks} {departments}>
    <div class="bg-neutral-50 py-12 dark:bg-[#161615]">
        <div class="mx-auto max-w-7xl px-6">
            <h1 class="text-4xl font-extrabold tracking-tight text-neutral-900 dark:text-white">Contact & Admissions</h1>
            <p class="mt-2 text-neutral-500 dark:text-neutral-400">Get in touch with our admissions coordinators, deans, or administrative offices.</p>
        </div>
    </div>

    <section class="py-16 md:py-24">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
                <!-- Contact info card -->
                <div class="space-y-8">
                    <div>
                        <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">Campus Information</h2>
                        <p class="mt-4 text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            Have questions about enrolment, tuition fees, course curriculum, or campus life? Reach out to the specific offices below.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start gap-4 rounded-xl border border-black/5 p-5 dark:border-white/5 dark:bg-[#161615]">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-600/10 text-amber-600">
                                <MapPin class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-neutral-900 dark:text-white">Campus Address</h3>
                                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                                    San Isidro College, Impalambong, Malaybalay City, Bukidnon, Philippines 8700
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 rounded-xl border border-black/5 p-5 dark:border-white/5 dark:bg-[#161615]">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-600/10 text-amber-600">
                                <Phone class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-neutral-900 dark:text-white">Phone Contacts</h3>
                                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                                    Registrar: +63 (88) 813-2244 <br />
                                    Admissions Office: +63 (88) 813-2245 <br />
                                    Finance Desk: +63 (88) 813-2246
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 rounded-xl border border-black/5 p-5 dark:border-white/5 dark:bg-[#161615]">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-600/10 text-amber-600">
                                <Mail class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-neutral-900 dark:text-white">Email Addresses</h3>
                                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                                    General Inquiries: info@sic.edu.ph <br />
                                    Registrar: registrar@sic.edu.ph <br />
                                    Admissions Support: admissions@sic.edu.ph
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 rounded-xl border border-black/5 p-5 dark:border-white/5 dark:bg-[#161615]">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-600/10 text-amber-600">
                                <Clock class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-bold text-neutral-900 dark:text-white">Office Hours</h3>
                                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                                    Monday - Friday: 8:00 AM - 5:00 PM <br />
                                    Saturday (Admissions Only): 8:30 AM - 12:00 PM
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form card -->
                <div class="rounded-2xl border border-black/5 bg-white p-8 shadow-sm dark:border-white/5 dark:bg-[#161615]">
                    <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">Send us a Message</h2>
                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                        Fill out the form below and our customer desk will get back to you shortly.
                    </p>

                    {#if submitted}
                        <div class="mt-6 rounded-xl bg-green-50 p-4 text-sm text-green-700 dark:bg-green-950/20 dark:text-green-400">
                            Thank you! Your message has been sent successfully. We will contact you soon.
                        </div>
                    {/if}

                    <form onsubmit={handleSubmit} class="mt-6 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-semibold mb-1.5">Full Name</label>
                            <input 
                                id="name"
                                type="text" 
                                required
                                bind:value={name} 
                                class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                                placeholder="Your Name"
                            />
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold mb-1.5">Email Address</label>
                            <input 
                                id="email"
                                type="email" 
                                required
                                bind:value={email} 
                                class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                                placeholder="name@example.com"
                            />
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-semibold mb-1.5">Message / Inquiry</label>
                            <textarea 
                                id="message"
                                rows="5"
                                required
                                bind:value={message} 
                                class="w-full rounded-xl border border-neutral-200 px-4 py-2.5 text-sm outline-none focus:border-amber-600 dark:border-neutral-800 dark:bg-[#0a0a0a]"
                                placeholder="How can we help you?"
                            ></textarea>
                        </div>

                        <button 
                            type="submit" 
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-amber-600 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-600/10 hover:bg-amber-700 transition-colors"
                        >
                            <Send class="h-4 w-4" /> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</PublicLayout>
