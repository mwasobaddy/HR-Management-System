import { Form, Head } from '@inertiajs/react';

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
    return (
        <>
            <Head title={`Subscribe to ${plan.name}`} />
            <div className="min-h-screen flex items-center justify-center bg-background p-4">
                <div className="w-full max-w-2xl">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-2xl">Complete Your Subscription</CardTitle>
                            <CardDescription>
                                You're subscribing to the <strong>{plan.name}</strong> plan - ${plan.price}/{plan.billing_cycle}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Form
                                action="/subscribe"
                                method="post"
                                disableWhileProcessing
                                className="flex flex-col gap-6"
                            >
                                {({ processing, errors }) => (
                                    <>
                                        <input type="hidden" name="plan_id" value={plan.id} />

                                        <div className="grid gap-6">
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

                                            {/* Summary */}
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

                                            <Button
                                                type="submit"
                                                size="lg"
                                                className="w-full"
                                                disabled={processing}
                                            >
                                                {processing && <Spinner />}
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
