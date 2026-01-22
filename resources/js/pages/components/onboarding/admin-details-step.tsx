import { AlertCircle, Eye, EyeOff } from 'lucide-react';
import { useState } from 'react';

import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import languages from '@/data/languages.json';

interface AdminDetailsStepProps {
    formData: {
        first_name: string;
        last_name: string;
        personal_email: string;
        work_email: string;
        language: string;
        password: string;
        password_confirmation: string;
    };
    errors: Record<string, string>;
    originalEmail: string;
    onChange: (field: string, value: unknown) => void;
    onEmailChange: (newEmail: string) => void;
}

export default function AdminDetailsStep({
    formData,
    errors,
    originalEmail,
    onChange,
    onEmailChange,
}: AdminDetailsStepProps) {
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);

    return (
        <div className="space-y-4">
            <div className="grid md:grid-cols-2 gap-6">
                <div className='grid gap-2'>
                    <Label htmlFor="first_name">First Name <span className="text-red-600">*</span></Label>
                    <Input
                        id="first_name"
                        value={formData.first_name}
                        onChange={(e) => onChange('first_name', e.target.value)}
                        required
                        placeholder="John"
                    />
                    <InputError message={errors.first_name} />
                </div>

                <div className='grid gap-2'>
                    <Label htmlFor="last_name">Last Name <span className="text-red-600">*</span></Label>
                    <Input
                        id="last_name"
                        value={formData.last_name}
                        onChange={(e) => onChange('last_name', e.target.value)}
                        required
                        placeholder="Doe"
                    />
                    <InputError message={errors.last_name} />
                </div>

                <div className="grid gap-2 md:col-span-2">
                    <Label htmlFor="personal_email">Personal Email (Login Email) <span className="text-red-600">*</span></Label>
                    <Input
                        id="personal_email"
                        type="email"
                        value={formData.personal_email}
                        onChange={(e) => onEmailChange(e.target.value)}
                        required
                        placeholder="john@example.com"
                    />
                    {formData.personal_email !== originalEmail && (
                        <div className="flex items-start gap-2 mt-2 p-3 bg-amber-50 border border-amber-200 rounded-md">
                            <AlertCircle className="h-5 w-5 text-amber-600 shrink-0 mt-0.5" />
                            <p className="text-sm text-amber-800">
                                Changing your email will log you out and require verification of your new email address.
                            </p>
                        </div>
                    )}
                    <InputError message={errors.personal_email} />
                </div>

                <div className="grid gap-2 md:col-span-2">
                    <Label htmlFor="work_email">Work Email <span className="text-red-600">*</span></Label>
                    <Input
                        id="work_email"
                        type="email"
                        value={formData.work_email}
                        onChange={(e) => onChange('work_email', e.target.value)}
                        placeholder="john@company.com"
                    />
                    <InputError message={errors.work_email} />
                </div>

                <div className="grid gap-2 md:col-span-2">
                    <Label htmlFor="language">Preferred Language <span className="text-red-600">*</span></Label>
                    <Select value={formData.language} onValueChange={(value) => onChange('language', value)}>
                        <SelectTrigger>
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent className="max-h-60">
                            {languages.map((lang) => (
                                <SelectItem key={lang.code} value={lang.code}>
                                    {lang.name} ({lang.native_name})
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.language} />
                </div>

                <div className='grid gap-2'>
                    <Label htmlFor="password">New Password <span className="text-red-600">*</span></Label>
                    <div className="relative">
                        <Input
                            id="password"
                            type={showPassword ? 'text' : 'password'}
                            value={formData.password}
                            onChange={(e) => onChange('password', e.target.value)}
                            required
                            pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$"
                            placeholder="Enter password"
                        />
                        <button
                            type="button"
                            onClick={() => setShowPassword(!showPassword)}
                            className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                        </button>
                    </div>
                    <p className="text-sm text-blue-600">Min 8 characters, must include letters and numbers</p>
                    <InputError message={errors.password} />
                </div>

                <div className='grid gap-2'>
                    <Label htmlFor="password_confirmation">Confirm Password <span className="text-red-600">*</span></Label>
                    <div className="relative">
                        <Input
                            id="password_confirmation"
                            type={showConfirmPassword ? 'text' : 'password'}
                            value={formData.password_confirmation}
                            onChange={(e) => onChange('password_confirmation', e.target.value)}
                            required
                            placeholder="Confirm password"
                        />
                        <button
                            type="button"
                            onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                            className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            {showConfirmPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                        </button>
                    </div>
                    <p className="text-sm text-blue-600" style={{ visibility: 'hidden' }}>Min 8 characters, must include letters and numbers</p>
                    <InputError message={errors.password_confirmation} />
                </div>
            </div>
        </div>
    );
}
