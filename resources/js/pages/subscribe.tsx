import { Form, Head } from '@inertiajs/react';
import { ArrowLeft, CircleCheckBig, BadgeCheck } from 'lucide-react';
import { useState } from 'react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Spinner } from '@/components/ui/spinner';






interface SubscriptionPlan {
    id: number;
    name: string;
    price: string;
    billing_cycle: string;
    max_users: number;
    features: string[];
}

export default function Subscribe({ plan }: { plan: SubscriptionPlan }) {
    const [step] = useState(1); // For future multi-step, keep onboarding look
    return (
        <>
            <Head title={`Subscribe to ${plan.name}`} />
            <div className="min-h-screen bg-zinc-50 dark:bg-neutral-900 py-8 px-4">
                <div className="max-w-7xl mx-auto grid grid-cols-7 gap-8">
                    {/* Welcome/Info Card */}
                    <Card className="shadow-xl col-span-7 md:col-span-3 px-8 bg-linear-to-t dark:from-neutral-900 dark:to-neutral-800 from-red-50 to-purple-50 dark:border-neutral-700 border">
                        <div className="flex gap-4 mb-8 pt-6 items-center">
                            <div className="flex justify-center mb-4">
                                <div className="h-8 w-8 bg-linear-to-r from-blue-600 to-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                                    <BadgeCheck className="h-8 w-8 text-white" />
                                </div>
                            </div>
                            <div>
                                <h1 className="text-xl font-bold mb-0">
                                    Subscribe to {plan.name}
                                </h1>
                                <p className="text-muted-foreground">
                                    Complete your subscription to unlock all features for your team.
                                </p>
                            </div>
                        </div>
                        <div className="mb-8">
                            <div className='text-md uppercase mb-2 font-black! text-purple-800 dark:text-purple-400'>
                                PLAN SUMMARY
                            </div>
                            <div className='h-1 bg-linear-to-r from-red-500 to-purple-500 rounded-full mb-2 w-24'></div>
                            <div className="rounded-lg bg-muted p-4 space-y-2">
                                <div className="flex justify-between">
                                    <span className="font-medium">Plan:</span>
                                    <span>{plan.name}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="font-medium">Max Users:</span>
                                    <span>{plan.max_users}</span>
                                </div>
                                <div className="flex justify-between text-lg font-bold">
                                    <span>Total:</span>
                                    <span>${plan.price}/{plan.billing_cycle}</span>
                                </div>
                            </div>
                        </div>
                    </Card>

                    {/* Form Card */}
                    <Card className="col-span-7 md:col-span-4 bg-transparent border-0 shadow-none">
                        <CardHeader>
                            <CardTitle className="">
                                <p className='text-md uppercase mb-2 font-black! text-purple-800 dark:text-purple-400'>
                                    STEP {step} OF 1
                                </p>
                                <h2 className='text-2xl'>Complete Your Subscription</h2>
                                <div className='h-1 bg-linear-to-r from-red-500 to-purple-500 rounded-full mb-2 w-24'></div>
                            </CardTitle>
                            <CardDescription>
                                You're subscribing to the <strong>{plan.name}</strong> plan - ${plan.price}/{plan.billing_cycle}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Form
                                action="/subscribe"
                                method="post"
                                disableWhileProcessing
                                className="space-y-6"
                            >
                                {({ processing, errors }) => (
                                    <>
                                        <input type="hidden" name="plan_id" value={plan.id} />

                                        {/* Company Information */}
                                        <div className="space-y-4">
                                            <h3 className="text-lg font-semibold">Company Information</h3>
                                            <div className="grid gap-2">
                                                <Label htmlFor="company_name">Company Name *</Label>
                                                <Input
                                                    id="company_name"
                                                    name="company_name"
                                                    type="text"
                                                    required
                                                    autoFocus
                                                    placeholder="Acme Corporation"
                                                />
                                                <InputError message={errors.company_name} />
                                            </div>
                                            <div className="grid gap-2">
                                                <Label htmlFor="email">Business Email *</Label>
                                                <Input
                                                    id="email"
                                                    name="email"
                                                    type="email"
                                                    required
                                                    placeholder="admin@company.com"
                                                />
                                                <InputError message={errors.email} />
                                            </div>
                                            <div className="grid gap-2">
                                                <Label htmlFor="admin_name">Administrator Name *</Label>
                                                <Input
                                                    id="admin_name"
                                                    name="admin_name"
                                                    type="text"
                                                    required
                                                    placeholder="John Doe"
                                                />
                                                <InputError message={errors.admin_name} />
                                            </div>
                                        </div>

                                        {/* Payment Information */}
                                        {plan.price !== '0' && (
                                            <div className="space-y-4">
                                                <h3 className="text-lg font-semibold">Payment Information</h3>
                                                <div className="grid gap-2">
                                                    <Label>Payment Type *</Label>
                                                    <RadioGroup defaultValue="recurring" name="payment_type">
                                                        <div className="flex items-center space-x-2">
                                                            <RadioGroupItem value="recurring" id="recurring" />
                                                            <Label htmlFor="recurring" className="font-normal cursor-pointer">
                                                                Recurring (Auto-renew monthly)
                                                            </Label>
                                                        </div>
                                                        <div className="flex items-center space-x-2">
                                                            <RadioGroupItem value="one-time" id="one-time" />
                                                            <Label htmlFor="one-time" className="font-normal cursor-pointer">
                                                                One-time payment (Renew manually)
                                                            </Label>
                                                        </div>
                                                    </RadioGroup>
                                                    <InputError message={errors.payment_type} />
                                                </div>
                                                <div className="grid gap-2">
                                                    <Label htmlFor="card_number">Card Number *</Label>
                                                    <Input
                                                        id="card_number"
                                                        name="card_number"
                                                        type="text"
                                                        required
                                                        placeholder="4242 4242 4242 4242"
                                                        maxLength={19}
                                                    />
                                                    <InputError message={errors.card_number} />
                                                </div>
                                                <div className="grid grid-cols-2 gap-4">
                                                    <div className="grid gap-2">
                                                        <Label htmlFor="expiry">Expiry Date *</Label>
                                                        <Input
                                                            id="expiry"
                                                            name="expiry"
                                                            type="text"
                                                            required
                                                            placeholder="MM/YY"
                                                            maxLength={5}
                                                        />
                                                        <InputError message={errors.expiry} />
                                                    </div>
                                                    <div className="grid gap-2">
                                                        <Label htmlFor="cvv">CVV *</Label>
                                                        <Input
                                                            id="cvv"
                                                            name="cvv"
                                                            type="text"
                                                            required
                                                            placeholder="123"
                                                            maxLength={4}
                                                        />
                                                        <InputError message={errors.cvv} />
                                                    </div>
                                                </div>
                                            </div>
                                        )}

                                        {/* Info/summary */}
                                        <div className="text-sm text-muted-foreground">
                                            {plan.price === '0' ? (
                                                <p>
                                                    After submission, you'll receive an email with your login credentials. 
                                                    Your 14-day free trial starts immediately.
                                                </p>
                                            ) : (
                                                <p>
                                                    After payment, you'll receive an email with your login credentials. 
                                                    You can start using the system immediately.
                                                </p>
                                            )}
                                        </div>

                                        <div className="flex justify-between pt-6">
                                            <Button
                                                key="back"
                                                type="button"
                                                className='bg-primary dark:bg-red-500 dark:text-primary dark:hover:bg-red-600 transition-colors duration-200'
                                                disabled
                                            >
                                                <ArrowLeft className="h-4 w-4" />
                                                Back
                                            </Button>
                                            <Button
                                                key="submit"
                                                type="submit"
                                                size="lg"
                                                className="bg-blue-700 hover:bg-blue-600 dark:bg-primary dark:hover:bg-zinc-200 transition-colors duration-200 w-48"
                                                disabled={processing}
                                            >
                                                {processing && <Spinner />}
                                                <CircleCheckBig className="h-4 w-4" />
                                                {plan.price === '0' ? 'Start Free Trial' : `Pay $${plan.price} & Subscribe`}
                                            </Button>
                                        </div>
                                    </>
                                )}
                            </Form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}
