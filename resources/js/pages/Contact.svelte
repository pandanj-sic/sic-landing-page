<script lang="ts">
    import { Form, page } from '@inertiajs/svelte';
    import Phone from 'lucide-svelte/icons/phone';
    import Mail from 'lucide-svelte/icons/mail';
    import MapPin from 'lucide-svelte/icons/map-pin';
    import Clock from 'lucide-svelte/icons/clock';
    import CheckCircle2 from 'lucide-svelte/icons/check-circle-2';
    import Loader2 from 'lucide-svelte/icons/loader-2';

    import AppHead from '@/components/AppHead.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Card, CardContent } from '@/components/ui/card';
    import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
    import PageLayout from '@/layouts/PageLayout.svelte';
    import { store } from '@/actions/App/Http/Controllers/ContactController';

    // Access the flash message from Inertia page props
    const flashSuccess = $derived((page.props as any).flash?.success);
</script>

<AppHead title="Contact Us" />

<PageLayout>
    <div class="relative overflow-hidden bg-muted/40 py-16 lg:py-24 border-b">
        <div class="absolute inset-0 bg-gradient-to-tr from-primary/5 via-transparent to-primary/5 opacity-70"></div>
        <div class="relative mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <span class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary">
                Contact Us
            </span>
            <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-foreground sm:text-5xl lg:text-6xl">
                Get in Touch with SIC
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg text-muted-foreground">
                Have questions about admission requirements, scholarships, or academic offerings? We are here to help you. Reach out to our team today.
            </p>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-12">
            <!-- Contact Info / Map -->
            <div class="space-y-8 lg:col-span-5">
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold tracking-tight text-foreground">
                        Institutional Contact Information
                    </h2>
                    <p class="text-muted-foreground">
                        Feel free to contact us through any of the following channels or visit our administrative offices during working hours.
                    </p>
                </div>

                <div class="space-y-4">
                    <!-- Address -->
                    <Card class="bg-card/50 backdrop-blur-sm border border-border/60 transition-all duration-300 hover:border-primary/30 hover:shadow-md">
                        <CardContent class="flex items-start gap-4 p-5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <MapPin class="h-5 w-5" />
                            </div>
                            <div class="space-y-1">
                                <h3 class="font-semibold text-foreground text-sm">Campus Address</h3>
                                <p class="text-sm text-muted-foreground leading-relaxed">
                                    San Isidro College, Impalambong, Malaybalay City, Bukidnon, Philippines, 8700
                                </p>
                                <a
                                    href="https://maps.google.com/?q=San+Isidro+College+Malaybalay+Bukidnon"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center text-xs font-semibold text-primary hover:underline mt-1"
                                >
                                    Get Directions on Google Maps &rarr;
                                </a>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Phone Numbers -->
                    <Card class="bg-card/50 backdrop-blur-sm border border-border/60 transition-all duration-300 hover:border-primary/30 hover:shadow-md">
                        <CardContent class="flex items-start gap-4 p-5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <Phone class="h-5 w-5" />
                            </div>
                            <div class="space-y-1">
                                <h3 class="font-semibold text-foreground text-sm">Telephone & Fax</h3>
                                <p class="text-sm text-muted-foreground">
                                    (088) 813-2216 / (088) 813-2217
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Email Address -->
                    <Card class="bg-card/50 backdrop-blur-sm border border-border/60 transition-all duration-300 hover:border-primary/30 hover:shadow-md">
                        <CardContent class="flex items-start gap-4 p-5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <Mail class="h-5 w-5" />
                            </div>
                            <div class="space-y-1">
                                <h3 class="font-semibold text-foreground text-sm">Email Address</h3>
                                <p class="text-sm text-muted-foreground leading-relaxed">
                                    <span class="block">Institutional: <a href="mailto:info@sic.edu.ph" class="text-primary hover:underline">info@sic.edu.ph</a></span>
                                    <span class="block">Registrar: <a href="mailto:registrar@sic.edu.ph" class="text-primary hover:underline">registrar@sic.edu.ph</a></span>
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Working Hours -->
                    <Card class="bg-card/50 backdrop-blur-sm border border-border/60 transition-all duration-300 hover:border-primary/30 hover:shadow-md">
                        <CardContent class="flex items-start gap-4 p-5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <Clock class="h-5 w-5" />
                            </div>
                            <div class="space-y-1">
                                <h3 class="font-semibold text-foreground text-sm">Office Hours</h3>
                                <p class="text-sm text-muted-foreground">
                                    Monday &ndash; Friday: 8:00 AM &ndash; 5:00 PM
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    Saturday: 8:00 AM &ndash; 12:00 PM
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Interactive Map Frame -->
                <div class="overflow-hidden rounded-xl border border-border shadow-sm">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3947.5029054707663!2d125.12288301140925!3d8.152011191837497!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3300234c9c1b48b7%3A0xeab5e865f8072046!2sSan%20Isidro%20College!5e0!3m2!1sen!2sph!4v1717564000000!5m2!1sen!2sph"
                        width="100%"
                        height="320"
                        style="border:0;"
                        allowfullscreen={true}
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        class="w-full"
                        title="San Isidro College Campus Location"
                    ></iframe>
                </div>
            </div>

            <!-- Form Container -->
            <div class="lg:col-span-7">
                <Card class="border border-border/80 shadow-lg bg-card/60 backdrop-blur-md">
                    <CardContent class="p-6 sm:p-8 space-y-6">
                        <div class="space-y-2">
                            <h2 class="text-2xl font-bold text-foreground">Send us a Message</h2>
                            <p class="text-sm text-muted-foreground">
                                Have an inquiry? Fill in the form details below and we will route your message to the appropriate department.
                            </p>
                        </div>

                        {#if flashSuccess}
                            <Alert class="bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/30">
                                <CheckCircle2 class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                                <AlertTitle>Success</AlertTitle>
                                <AlertDescription>{flashSuccess}</AlertDescription>
                            </Alert>
                        {/if}

                        <Form
                            {...store.form()}
                            resetOnSuccess={['name', 'email', 'subject', 'message']}
                            class="space-y-5"
                        >
                            {#snippet children({ errors, processing })}
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="grid gap-2">
                                        <Label for="name">Full Name</Label>
                                        <Input
                                            id="name"
                                            name="name"
                                            type="text"
                                            required
                                            placeholder="Your full name"
                                            class="w-full"
                                        />
                                        <InputError message={errors.name} />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="email">Email Address</Label>
                                        <Input
                                            id="email"
                                            name="email"
                                            type="email"
                                            required
                                            placeholder="you@example.com"
                                            class="w-full"
                                        />
                                        <InputError message={errors.email} />
                                    </div>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="subject">Subject</Label>
                                    <Input
                                        id="subject"
                                        name="subject"
                                        type="text"
                                        placeholder="What is this regarding?"
                                        class="w-full"
                                    />
                                    <InputError message={errors.subject} />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="message">Your Message</Label>
                                    <textarea
                                        id="message"
                                        name="message"
                                        required
                                        rows={6}
                                        placeholder="Please write your inquiry here..."
                                        class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                    ></textarea>
                                    <InputError message={errors.message} />
                                </div>

                                <div class="pt-2">
                                    <Button
                                        type="submit"
                                        disabled={processing}
                                        class="w-full sm:w-auto min-w-[140px]"
                                    >
                                        {#if processing}
                                            <Loader2 class="mr-2 h-4 w-4 animate-spin" />
                                            Sending...
                                        {:else}
                                            Send Message
                                        {/if}
                                    </Button>
                                </div>
                            {/snippet}
                        </Form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</PageLayout>
