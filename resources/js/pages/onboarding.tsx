import { Head, router, useForm } from '@inertiajs/react';
import { Building2, User, Settings, Zap, Eye, EyeOff, CheckCircle2, AlertCircle } from 'lucide-react';
import { useState } from 'react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import currencies from '@/data/currencies.json';
import languages from '@/data/languages.json';

interface OnboardingProps {
    user: {
        id: number;
        name: string;
        email: string;
    };
    tenant: {
        id: string;
        name: string;
    };
    plan: {
        id: number;
        name: string;
        slug: string;
    };
}

export default function Onboarding({ user, tenant, plan }: OnboardingProps) {
    const [currentStep, setCurrentStep] = useState(1);
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);

    const isProOrEnterprise = ['pro', 'enterprise'].includes(plan.slug);

    const form = useForm({
        company_name: '',
        company_logo: null as File | null,
        address: '',
        address_line_2: '',
        city: '',
        state: '',
        country: '',
        postal_code: '',
        company_phone: '',
        company_email: '',
        fiscal_year_start: '01-01',
        currency: 'USD',
        first_name: user.name.split(' ')[0] || '',
        last_name: user.name.split(' ').slice(1).join(' ') || '',
        personal_email: user.email,
        work_email: '',
        language: 'en',
        password: '',
        password_confirmation: '',
        working_hours: {
            monday: { enabled: true, start: '09:00', end: '17:00' },
            tuesday: { enabled: true, start: '09:00', end: '17:00' },
            wednesday: { enabled: true, start: '09:00', end: '17:00' },
            thursday: { enabled: true, start: '09:00', end: '17:00' },
            friday: { enabled: true, start: '09:00', end: '17:00' },
            saturday: { enabled: false, start: '', end: '' },
            sunday: { enabled: false, start: '', end: '' },
        },
        branch_name: 'Main Branch',
        department_name: 'General',
        ai_provider: '',
        ai_model: '',
        ai_api_key: '',
        google_calendar_api_key: '',
        google_meet_api_key: '',
        smtp_host: '',
        smtp_port: 587,
        smtp_username: '',
        smtp_password: '',
        smtp_encryption: 'tls',
        smtp_from_address: '',
        smtp_from_name: '',
    });

    const steps = [
        { number: 1, title: 'Company Details', icon: Building2 },
        { number: 2, title: 'Admin Details', icon: User },
        { number: 3, title: 'Company Config', icon: Settings },
        ...(isProOrEnterprise ? [{ number: 4, title: 'API & Settings', icon: Zap }] : []),
    ];

    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    const handleNext = () => {
        if (currentStep < steps.length) {
            setCurrentStep(currentStep + 1);
        }
    };

    const handleBack = () => {
        if (currentStep > 1) {
            setCurrentStep(currentStep - 1);
        }
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post('/onboarding/complete', {
            preserveScroll: true,
            onSuccess: () => {
                router.visit('/dashboard');
            },
        });
    };

    const handleEmailChange = (newEmail: string) => {
        if (newEmail !== user.email) {
            if (!confirm('Changing your email will log you out and require verification. Continue?')) {
                return;
            }
        }
        form.setData('personal_email', newEmail);
    };

    return (
        <>
            <Head title="Complete Your Setup" />
            
            <div className="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8 px-4">
                <div className="max-w-5xl mx-auto">
                    {/* Welcome Header */}
                    <div className="text-center mb-8">
                        <div className="flex justify-center mb-4">
                            <div className="h-16 w-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                                <CheckCircle2 className="h-10 w-10 text-white" />
                            </div>
                        </div>
                        <h1 className="text-4xl font-bold text-gray-900 mb-2">
                            Welcome, {user.name}! 🎉
                        </h1>
                        <p className="text-lg text-gray-600">
                            Let's get your workspace set up in just a few steps
                        </p>
                    </div>

                    {/* Progress Steps */}
                    <div className="mb-8">
                        <div className="flex items-center justify-between max-w-3xl mx-auto">
                            {steps.map((step, index) => {
                                const Icon = step.icon;
                                const isActive = currentStep === step.number;
                                const isCompleted = currentStep > step.number;

                                return (
                                    <div key={step.number} className="flex items-center flex-1">
                                        <div className="flex flex-col items-center flex-1">
                                            <div
                                                className={`w-12 h-12 rounded-full flex items-center justify-center transition-all \${
                                                    isActive
                                                        ? 'bg-blue-600 text-white shadow-lg scale-110'
                                                        : isCompleted
                                                        ? 'bg-green-600 text-white'
                                                        : 'bg-gray-200 text-gray-500'
                                                }`}
                                            >
                                                {isCompleted ? (
                                                    <CheckCircle2 className="h-6 w-6" />
                                                ) : (
                                                    <Icon className="h-6 w-6" />
                                                )}
                                            </div>
                                            <span
                                                className={`mt-2 text-sm font-medium ${
                                                    isActive || isCompleted ? 'text-gray-900' : 'text-gray-500'
                                                }`}
                                            >
                                                {step.title}
                                            </span>
                                        </div>
                                        {index < steps.length - 1 && (
                                            <div
                                                className={`h-1 flex-1 mx-4 rounded transition-all ${
                                                    isCompleted ? 'bg-green-600' : 'bg-gray-200'
                                                }`}
                                            />
                                        )}
                                    </div>
                                );
                            })}
                        </div>
                    </div>

                    {/* Form Card */}
                    <Card className="shadow-xl">
                        <CardHeader>
                            <CardTitle className="text-2xl">
                                Step {currentStep}: {steps[currentStep - 1].title}
                            </CardTitle>
                            <CardDescription>
                                {currentStep === 1 && 'Tell us about your company'}
                                {currentStep === 2 && 'Set up your admin profile'}
                                {currentStep === 3 && 'Configure your company settings'}
                                {currentStep === 4 && 'Optional: Configure integrations and APIs'}
                            </CardDescription>
                        </CardHeader>

                        <CardContent>
                            <form onSubmit={handleSubmit} className="space-y-6">
                                {/* STEP 1: COMPANY DETAILS */}
                                {currentStep === 1 && (
                                    <div className="space-y-4">
                                        <div className="grid md:grid-cols-2 gap-4">
                                            <div className="md:col-span-2">
                                                <Label htmlFor="company_name">Company Name *</Label>
                                                <Input
                                                    id="company_name"
                                                    value={form.data.company_name}
                                                    onChange={(e) => form.setData('company_name', e.target.value)}
                                                    required
                                                    placeholder="Acme Corporation"
                                                />
                                                <InputError message={form.errors.company_name} />
                                            </div>

                                            <div className="md:col-span-2">
                                                <Label htmlFor="company_logo">Company Logo (Optional)</Label>
                                                <Input
                                                    id="company_logo"
                                                    type="file"
                                                    accept="image/*"
                                                    onChange={(e) => form.setData('company_logo', e.target.files?.[0] || null)}
                                                />
                                                <p className="text-sm text-gray-500 mt-1">Recommended: Square image, max 2MB</p>
                                            </div>

                                            <div className="md:col-span-2">
                                                <Label htmlFor="address">Address *</Label>
                                                <Input
                                                    id="address"
                                                    value={form.data.address}
                                                    onChange={(e) => form.setData('address', e.target.value)}
                                                    required
                                                    placeholder="123 Business Street"
                                                />
                                                <InputError message={form.errors.address} />
                                            </div>

                                            <div className="md:col-span-2">
                                                <Label htmlFor="address_line_2">Address Line 2</Label>
                                                <Input
                                                    id="address_line_2"
                                                    value={form.data.address_line_2}
                                                    onChange={(e) => form.setData('address_line_2', e.target.value)}
                                                    placeholder="Suite 100"
                                                />
                                            </div>

                                            <div>
                                                <Label htmlFor="city">City *</Label>
                                                <Input
                                                    id="city"
                                                    value={form.data.city}
                                                    onChange={(e) => form.setData('city', e.target.value)}
                                                    required
                                                    placeholder="New York"
                                                />
                                                <InputError message={form.errors.city} />
                                            </div>

                                            <div>
                                                <Label htmlFor="state">State/Province *</Label>
                                                <Input
                                                    id="state"
                                                    value={form.data.state}
                                                    onChange={(e) => form.setData('state', e.target.value)}
                                                    required
                                                    placeholder="NY"
                                                />
                                                <InputError message={form.errors.state} />
                                            </div>

                                            <div>
                                                <Label htmlFor="country">Country *</Label>
                                                <Input
                                                    id="country"
                                                    value={form.data.country}
                                                    onChange={(e) => form.setData('country', e.target.value)}
                                                    required
                                                    placeholder="United States"
                                                />
                                                <InputError message={form.errors.country} />
                                            </div>

                                            <div>
                                                <Label htmlFor="postal_code">Postal Code *</Label>
                                                <Input
                                                    id="postal_code"
                                                    value={form.data.postal_code}
                                                    onChange={(e) => form.setData('postal_code', e.target.value)}
                                                    required
                                                    placeholder="10001"
                                                />
                                                <InputError message={form.errors.postal_code} />
                                            </div>

                                            <div>
                                                <Label htmlFor="company_phone">Company Phone *</Label>
                                                <Input
                                                    id="company_phone"
                                                    type="tel"
                                                    value={form.data.company_phone}
                                                    onChange={(e) => form.setData('company_phone', e.target.value)}
                                                    required
                                                    placeholder="+1 (555) 123-4567"
                                                />
                                                <InputError message={form.errors.company_phone} />
                                            </div>

                                            <div>
                                                <Label htmlFor="company_email">Company Email *</Label>
                                                <Input
                                                    id="company_email"
                                                    type="email"
                                                    value={form.data.company_email}
                                                    onChange={(e) => form.setData('company_email', e.target.value)}
                                                    required
                                                    placeholder="contact@company.com"
                                                />
                                                <InputError message={form.errors.company_email} />
                                            </div>

                                            <div>
                                                <Label htmlFor="fiscal_year_start">Fiscal Year Start *</Label>
                                                <Select
                                                    value={form.data.fiscal_year_start}
                                                    onValueChange={(value) => form.setData('fiscal_year_start', value)}
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="01-01">January 1</SelectItem>
                                                        <SelectItem value="04-01">April 1</SelectItem>
                                                        <SelectItem value="07-01">July 1</SelectItem>
                                                        <SelectItem value="10-01">October 1</SelectItem>
                                                    </SelectContent>
                                                </Select>
                                                <InputError message={form.errors.fiscal_year_start} />
                                            </div>

                                            <div>
                                                <Label htmlFor="currency">Currency *</Label>
                                                <Select
                                                    value={form.data.currency}
                                                    onValueChange={(value) => form.setData('currency', value)}
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue />
                                                    </SelectTrigger>
                                                    <SelectContent className="max-h-60">
                                                        {currencies.map((currency) => (
                                                            <SelectItem key={currency.code} value={currency.code}>
                                                                {currency.code} - {currency.name} ({currency.symbol})
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                                <InputError message={form.errors.currency} />
                                            </div>
                                        </div>
                                    </div>
                                )}

                                {/* Navigation Buttons */}
                                <div className="flex justify-between pt-6 border-t">
                                    {currentStep > 1 && (
                                        <Button type="button" variant="outline" onClick={handleBack}>
                                            Back
                                        </Button>
                                    )}
                                    
                                    <div className="ml-auto flex gap-2">
                                        {currentStep < steps.length ? (
                                            <Button type="button" onClick={handleNext}>
                                                Next
                                            </Button>
                                        ) : (
                                            <Button type="submit" disabled={form.processing}>
                                                {form.processing ? 'Completing...' : 'Complete Setup'}
                                            </Button>
                                        )}
                                    </div>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}
