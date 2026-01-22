import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

import WorkingHoursGrid from './working-hours-grid';

interface CompanyConfigStepProps {
    formData: {
        working_hours: {
            [key: string]: {
                enabled: boolean;
                start: string;
                end: string;
            };
        };
        branch_name: string;
        department_name: string;
    };
    errors: Record<string, string>;
    onChange: (field: string, value: unknown) => void;
}

export default function CompanyConfigStep({ formData, errors, onChange }: CompanyConfigStepProps) {
    return (
        <div className="space-y-6">
            <WorkingHoursGrid
                workingHours={formData.working_hours}
                onChange={(workingHours) => onChange('working_hours', workingHours)}
            />

            <div className="grid md:grid-cols-2 gap-6">
                <div className='grid gap-2'>
                    <Label htmlFor="branch_name">Initial Branch Name <span className="text-red-600">*</span></Label>
                    <Input
                        id="branch_name"
                        value={formData.branch_name}
                        onChange={(e) => onChange('branch_name', e.target.value)}
                        required
                        placeholder="Main Branch"
                    />
                    <p className="text-sm text-blue-600">You can add more branches later</p>
                    <InputError message={errors.branch_name} />
                </div>

                <div className='grid gap-2'>
                    <Label htmlFor="department_name">Initial Department Name <span className="text-red-600">*</span></Label>
                    <Input
                        id="department_name"
                        value={formData.department_name}
                        onChange={(e) => onChange('department_name', e.target.value)}
                        required
                        placeholder="General"
                    />
                    <p className="text-sm text-blue-600">You can add more departments later</p>
                    <InputError message={errors.department_name} />
                </div>
            </div>
        </div>
    );
}
