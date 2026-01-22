import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import currencies from '@/data/currencies.json';

interface CompanyDetailsStepProps {
    formData: {
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
    };
    errors: Record<string, string>;
    onChange: (field: string, value: unknown) => void;
}

export default function CompanyDetailsStep({ formData, errors, onChange }: CompanyDetailsStepProps) {
    return (
        <div className="space-y-4">
            <div className="grid md:grid-cols-2 gap-6">
                <div className="grid gap-2 md:col-span-2">
                    <Label htmlFor="company_name">Company Name <span className="text-red-600">*</span></Label>
                    <Input
                        id="company_name"
                        value={formData.company_name}
                        onChange={(e) => onChange('company_name', e.target.value)}
                        required
                        placeholder="Acme Corporation"
                    />
                    <InputError message={errors.company_name} />
                </div>

                <div className="grid gap-2 md:col-span-2">
                    <Label htmlFor="company_logo">Company Logo (Optional)</Label>
                    <Input
                        id="company_logo"
                        type="file"
                        accept="image/*"
                        onChange={(e) => onChange('company_logo', e.target.files?.[0] || null)}
                    />
                    <p className="text-sm text-blue-600">Recommended: Square image, max 2MB</p>
                </div>

                <div className="grid gap-2 md:col-span-2">
                    <Label htmlFor="address">Address Line 1 <span className="text-red-600">*</span></Label>
                    <Input
                        id="address"
                        value={formData.address}
                        onChange={(e) => onChange('address', e.target.value)}
                        required
                        placeholder="123 Business Street"
                    />
                    <InputError message={errors.address} />
                </div>

                <div className="grid gap-2 md:col-span-2">
                    <Label htmlFor="address_line_2">Address Line 2</Label>
                    <Input
                        id="address_line_2"
                        value={formData.address_line_2}
                        onChange={(e) => onChange('address_line_2', e.target.value)}
                        placeholder="Suite 100"
                    />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="city">City <span className="text-red-600">*</span></Label>
                    <Input
                        id="city"
                        value={formData.city}
                        onChange={(e) => onChange('city', e.target.value)}
                        required
                        placeholder="New York"
                    />
                    <InputError message={errors.city} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="state">State/Province <span className="text-red-600">*</span></Label>
                    <Input
                        id="state"
                        value={formData.state}
                        onChange={(e) => onChange('state', e.target.value)}
                        required
                        placeholder="NY"
                    />
                    <InputError message={errors.state} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="country">Country <span className="text-red-600">*</span></Label>
                    <Input
                        id="country"
                        value={formData.country}
                        onChange={(e) => onChange('country', e.target.value)}
                        required
                        placeholder="United States"
                    />
                    <InputError message={errors.country} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="postal_code">Postal Code <span className="text-red-600">*</span></Label>
                    <Input
                        id="postal_code"
                        value={formData.postal_code}
                        onChange={(e) => onChange('postal_code', e.target.value)}
                        required
                        placeholder="10001"
                    />
                    <InputError message={errors.postal_code} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="company_phone">Company Phone <span className="text-red-600">*</span></Label>
                    <Input
                        id="company_phone"
                        type="tel"
                        value={formData.company_phone}
                        onChange={(e) => onChange('company_phone', e.target.value)}
                        required
                        placeholder="+1 (555) 123-4567"
                    />
                    <InputError message={errors.company_phone} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="company_email">Company Email <span className="text-red-600">*</span></Label>
                    <Input
                        id="company_email"
                        type="email"
                        value={formData.company_email}
                        onChange={(e) => onChange('company_email', e.target.value)}
                        required
                        placeholder="contact@company.com"
                    />
                    <InputError message={errors.company_email} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="fiscal_year_start">Fiscal Year Start <span className="text-red-600">*</span></Label>
                    <Select
                        value={formData.fiscal_year_start}
                        onValueChange={(value) => onChange('fiscal_year_start', value)}
                    >
                        <SelectTrigger>
                            <SelectValue Placeholder="Select a date"/>
                        </SelectTrigger>
                        <SelectContent>
                                <SelectItem value="01-01">January 1</SelectItem>
                                <SelectItem value="04-01">April 1</SelectItem>
                                <SelectItem value="07-01">July 1</SelectItem>
                                <SelectItem value="10-01">October 1</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError message={errors.fiscal_year_start} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="currency">Currency <span className="text-red-600">*</span></Label>
                    <Select
                        value={formData.currency}
                        onValueChange={(value) => onChange('currency', value)}
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
                    <InputError message={errors.currency} />
                </div>
            </div>
        </div>
    );
}
