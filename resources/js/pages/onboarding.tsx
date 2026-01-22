import { Head, router, useForm } from '@inertiajs/react';
import { Building2, User, Settings, Zap, BadgeCheck, ArrowLeft, CircleCheckBig } from 'lucide-react';
import { useState, type FormEvent, type MouseEvent, useEffect, useRef } from 'react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { useFormPersistence } from '@/hooks/use-form-persistence';

import AdminDetailsStep from './components/onboarding/admin-details-step';
import APISettingsStep from './components/onboarding/api-settings-step';
import CompanyConfigStep from './components/onboarding/company-config-step';
import CompanyDetailsStep from './components/onboarding/company-details-step';
import OnboardingStepIndicator from './components/onboarding/onboarding-step-indicator';

import {
    AlertDialog,
    // AlertDialogAction,
    // AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';


type WorkingDaySchedule = {
    enabled: boolean;
    start: string;
    end: string;
};

type WorkingHoursSchedule = {
    monday: WorkingDaySchedule;
    tuesday: WorkingDaySchedule;
    wednesday: WorkingDaySchedule;
    thursday: WorkingDaySchedule;
    friday: WorkingDaySchedule;
    saturday: WorkingDaySchedule;
    sunday: WorkingDaySchedule;
};

interface OnboardingFormData {
    company_name: string;
    company_logo: File | null;
    address: string;
    address_line_2: string;
    city: string;
    state: string;
    country: string;
    postal_code: string;
    company_phone: string;
    company_email: string;
    fiscal_year_start: string;
    currency: string;
    first_name: string;
    last_name: string;
    personal_email: string;
    work_email: string;
    language: string;
    password: string;
    password_confirmation: string;
    working_hours: WorkingHoursSchedule;
    branch_name: string;
    department_name: string;
    ai_provider: string;
    ai_model: string;
    ai_api_key: string;
    google_calendar_api_key: string;
    google_meet_api_key: string;
    smtp_host: string;
    smtp_port: number;
    smtp_username: string;
    smtp_password: string;
    smtp_encryption: string;
    smtp_from_address: string;
    smtp_from_name: string;
}

interface OnboardingProps {
    user: {
        id: number;
        name: string;
        email: string;
    };
    tenant: {
        id: string;
        name: string;
    } | null;
    plan: {
        id: number;
        name: string;
        slug: string;
    };
}

export default function Onboarding({ user, tenant, plan }: OnboardingProps) {
    const [currentStep, setCurrentStep] = useState(1);
    const [showRestoreDialog, setShowRestoreDialog] = useState(false);
    const [hasAcknowledgedRestore, setHasAcknowledgedRestore] = useState(false);
    const savedStepRef = useRef<number | null>(null);

    const isProOrEnterprise = ['pro', 'enterprise'].includes(plan.slug);
    const stepStorageKey = `onboarding-step-${user.id}`;

    const form = useForm<OnboardingFormData>({
        company_name: '',
        company_logo: null,
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
            saturday: { enabled: false, start: '09:00', end: '13:00' },
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

    // Form persistence with session storage
    const { savedData, hasSavedData, clearSavedData } = useFormPersistence({
        storageKey: `onboarding-draft-${user.id}`,
        formData: form.data,
        enabled: true,
    });

    // Cache saved step on mount
    useEffect(() => {
        const storedStep = Number(sessionStorage.getItem(stepStorageKey));
        if (!Number.isNaN(storedStep) && storedStep >= 1) {
            savedStepRef.current = storedStep;
        }
    }, [stepStorageKey]);

    // Check for saved data on mount and show restore dialog when needed
    useEffect(() => {
        if (hasSavedData && savedData) {
            // Use a microtask to avoid cascading renders
            queueMicrotask(() => {
                setShowRestoreDialog(true);
            });
        } else {
            queueMicrotask(() => {
                setHasAcknowledgedRestore(true);
            });
        }
    }, [hasSavedData, savedData]);

    // Handle restore dialog confirmation
    const handleRestoreData = () => {
        if (savedData) {
            // Restore all form data
            Object.entries(savedData).forEach(([key, value]) => {
                applyFormChange(key as keyof OnboardingFormData, value as OnboardingFormData[keyof OnboardingFormData]);
            });
            const storedStep = savedStepRef.current ?? Number(sessionStorage.getItem(stepStorageKey));
            if (!Number.isNaN(storedStep) && storedStep >= 1) {
                setCurrentStep(Math.min(storedStep, steps.length));
            }
            setHasAcknowledgedRestore(true);
            setShowRestoreDialog(false);
        }
    };

    const handleDiscardData = () => {
        clearSavedData();
        sessionStorage.removeItem(stepStorageKey);
        savedStepRef.current = null;
        setCurrentStep(1);
        setHasAcknowledgedRestore(true);
        setShowRestoreDialog(false);
    };

    const applyFormChange = <K extends keyof OnboardingFormData>(
        field: K,
        value: OnboardingFormData[K],
    ) => {
        form.setData((data) => ({
            ...data,
            [field]: value,
        }));
    };

    // Persist current step so we can restore where user left off
    useEffect(() => {
        if (!hasAcknowledgedRestore) {
            return;
        }
        sessionStorage.setItem(stepStorageKey, currentStep.toString());
        return () => {};
    }, [currentStep, hasAcknowledgedRestore, stepStorageKey]);

    const handleFormChange: (field: string, value: unknown) => void = (field, value) => {
        applyFormChange(
            field as keyof OnboardingFormData,
            value as OnboardingFormData[keyof OnboardingFormData],
        );
    };

    const steps = [
        { 
            number: 1, 
            title: 'Company Details', 
            description: 'Add your company information including name, address, and basic contact details to set up your workspace.',
            icon: Building2 
        },
        { 
            number: 2, 
            title: 'Admin Details', 
            description: 'Set up your administrator profile with personal information, credentials, and preferred language settings.',
            icon: User 
        },
        { 
            number: 3, 
            title: 'Company Config', 
            description: 'Configure your working hours, departments, and organizational structure to match your business operations.',
            icon: Settings 
        },
        ...(isProOrEnterprise ? [{ 
            number: 4, 
            title: 'API & Settings', 
            description: 'Connect third-party services and configure API integrations for enhanced functionality and automation.',
            icon: Zap 
        }] : []),
    ];

    const handleNext = (event: MouseEvent<HTMLButtonElement>) => {
        event.preventDefault();
        if (currentStep < steps.length) {
            setCurrentStep((previous) => Math.min(previous + 1, steps.length));
        }
    };

    const handleBack = (event: MouseEvent<HTMLButtonElement>) => {
        event.preventDefault();
        if (currentStep > 1) {
            setCurrentStep((previous) => Math.max(previous - 1, 1));
        }
    };

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        
        // Only submit if on the last step
        if (currentStep !== steps.length) {
            return;
        }
        
        form.post('/onboarding/complete', {
            preserveScroll: true,
            onSuccess: () => {
                clearSavedData();
                sessionStorage.removeItem(stepStorageKey);
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
        applyFormChange('personal_email', newEmail);
    };

    return (
        <>
            <Head title="Complete Your Setup" />

            {/* Restore Progress Dialog */}
            <AlertDialog open={showRestoreDialog} onOpenChange={setShowRestoreDialog}>
                <AlertDialogContent className="bg-linear-to-t dark:from-neutral-900 dark:to-neutral-800 from-red-50 to-purple-50 dark:border-neutral-700 border">
                    <AlertDialogHeader>
                        <AlertDialogTitle className="flex items-center gap-2">
                            <CircleCheckBig className="h-5 w-5 text-blue-600" />
                            Continue Where You Left Off?
                        </AlertDialogTitle>
                        <AlertDialogDescription>
                            We found your saved progress from a previous session. Would you like to continue from where you left off, or start fresh?
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <Button
                            onClick={handleDiscardData}
                            className='bg-primary dark:bg-red-500 dark:text-primary dark:hover:bg-red-600 transition-colors duration-200'
                        >
                            Start Fresh
                        </Button>
                        <Button
                            onClick={handleRestoreData}
                            className='bg-blue-700 hover:bg-blue-600 dark:bg-primary dark:hover:bg-zinc-200 transition-colors duration-200'
                        >
                            Continue Progress
                        </Button>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
            
            <div className="min-h-screen bg-zinc-50 dark:bg-neutral-900 py-8 px-4">
                <div className="max-w-7xl mx-auto grid grid-cols-7 gap-8">
                    <Card className="shadow-xl col-span-7 md:col-span-3 px-8 bg-linear-to-t dark:from-neutral-900 dark:to-neutral-800 from-red-50 to-purple-50 dark:border-neutral-700 border">
                        {/* Welcome Header */}
                        <div className="flex gap-4 mb-8 pt-6 items-center">
                            <div className="flex justify-center mb-4">
                                <div className="h-8 w-8 bg-linear-to-r from-blue-600 to-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                                    <BadgeCheck className="h-8 w-8 text-white" />
                                </div>
                            </div>
                            <div>
                                <h1 className="text-xl font-bold mb-0">
                                    Welcome, {user.name}!
                                </h1>
                                <p className="text-muted-foreground">
                                    Let's get {tenant ? `the ${tenant.name}` : 'your'} workspace set up in just a few steps
                                </p>
                            </div>
                        </div>

                        {/* Progress Steps */}
                        <div className="mb-8">
                            <OnboardingStepIndicator steps={steps} currentStep={currentStep} />
                        </div>
                    </Card>

                    {/* Form Card */}
                    <Card className="col-span-7 md:col-span-4 bg-transparent border-0 shadow-none">
                        <CardHeader>
                            <CardTitle className="">
                                <p className='text-md uppercase mb-2 font-black! text-purple-800 dark:text-purple-400'>
                                    STEP {currentStep} OF {steps.length}
                                </p>
                                <h2 className='text-2xl'>
                                    {steps[currentStep - 1].title}
                                </h2>
                                <div className='h-1 bg-linear-to-r from-red-500 to-purple-500 rounded-full mb-2 w-24'>
                                </div>
                            </CardTitle>
                            <CardDescription>
                                {steps[currentStep - 1].description}
                            </CardDescription>
                        </CardHeader>

                        <CardContent>
                            <form onSubmit={handleSubmit} className="space-y-6">
                                {/* STEP 1: COMPANY DETAILS */}
                                {currentStep === 1 && (
                                    <CompanyDetailsStep
                                        formData={{
                                            company_name: form.data.company_name,
                                            company_logo: form.data.company_logo,
                                            address: form.data.address,
                                            address_line_2: form.data.address_line_2,
                                            city: form.data.city,
                                            state: form.data.state,
                                            country: form.data.country,
                                            postal_code: form.data.postal_code,
                                            company_phone: form.data.company_phone,
                                            company_email: form.data.company_email,
                                            fiscal_year_start: form.data.fiscal_year_start,
                                            currency: form.data.currency,
                                        }}
                                        errors={form.errors}
                                        onChange={handleFormChange}
                                    />
                                )}

                                {/* STEP 2: ADMIN DETAILS */}
                                {currentStep === 2 && (
                                    <AdminDetailsStep
                                        formData={{
                                            first_name: form.data.first_name,
                                            last_name: form.data.last_name,
                                            personal_email: form.data.personal_email,
                                            work_email: form.data.work_email,
                                            language: form.data.language,
                                            password: form.data.password,
                                            password_confirmation: form.data.password_confirmation,
                                        }}
                                        errors={form.errors}
                                        originalEmail={user.email}
                                        onChange={handleFormChange}
                                        onEmailChange={handleEmailChange}
                                    />
                                )}

                                {/* STEP 3: COMPANY CONFIG */}
                                {currentStep === 3 && (
                                    <CompanyConfigStep
                                        formData={{
                                            working_hours: form.data.working_hours,
                                            branch_name: form.data.branch_name,
                                            department_name: form.data.department_name,
                                        }}
                                        errors={form.errors}
                                        onChange={handleFormChange}
                                    />
                                )}

                                {/* STEP 4: API & SETTINGS (Pro/Enterprise Only) */}
                                {currentStep === 4 && isProOrEnterprise && (
                                    <APISettingsStep
                                        formData={{
                                            ai_provider: form.data.ai_provider,
                                            ai_model: form.data.ai_model,
                                            ai_api_key: form.data.ai_api_key,
                                            google_calendar_api_key: form.data.google_calendar_api_key,
                                            google_meet_api_key: form.data.google_meet_api_key,
                                            smtp_host: form.data.smtp_host,
                                            smtp_port: form.data.smtp_port,
                                            smtp_username: form.data.smtp_username,
                                            smtp_password: form.data.smtp_password,
                                            smtp_encryption: form.data.smtp_encryption,
                                            smtp_from_address: form.data.smtp_from_address,
                                            smtp_from_name: form.data.smtp_from_name,
                                        }}
                                        onChange={handleFormChange}
                                    />
                                )}

                                {/* Navigation Buttons */}
                                <div className="flex justify-between pt-6">
                                    <Button
                                        key="back"
                                        type="button"
                                        className='bg-primary dark:bg-red-500 dark:text-primary dark:hover:bg-red-600 transition-colors duration-200'
                                        onClick={handleBack}
                                        disabled={currentStep === 1}
                                    >
                                        <ArrowLeft className="h-4 w-4" />
                                        Back
                                    </Button>

                                    {currentStep < steps.length ? (
                                        <Button 
                                            key="next"
                                            type="button" 
                                            onClick={handleNext} 
                                            className='bg-blue-700 hover:bg-blue-600 dark:bg-primary dark:hover:bg-zinc-200 transition-colors duration-200'
                                        >
                                            Save & Continue
                                        </Button>
                                    ) : (
                                        <Button 
                                            key="submit"
                                            type="submit" 
                                            disabled={form.processing}
                                            className='bg-blue-700 hover:bg-blue-600 dark:bg-primary dark:hover:bg-zinc-200 transition-colors duration-200'
                                        >
                                            <CircleCheckBig className="h-4 w-4" />
                                            {form.processing ? 'Completing...' : 'Complete Setup'}
                                        </Button>
                                    )}
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}