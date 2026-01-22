import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface WorkingHoursData {
    [key: string]: {
        enabled: boolean;
        start: string;
        end: string;
    };
}

interface WorkingHoursGridProps {
    workingHours: WorkingHoursData;
    onChange: (workingHours: WorkingHoursData) => void;
}

const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

export default function WorkingHoursGrid({ workingHours, onChange }: WorkingHoursGridProps) {
    const handleDayToggle = (day: string, enabled: boolean) => {
        onChange({
            ...workingHours,
            [day]: { ...workingHours[day], enabled },
        });
    };

    const handleTimeChange = (day: string, field: 'start' | 'end', value: string) => {
        onChange({
            ...workingHours,
            [day]: { ...workingHours[day], [field]: value },
        });
    };

    return (
        <div>
            <Label className="text-base font-semibold mb-4 block">Working Hours *</Label>
            <div className="space-y-3">
                {days.map((day) => {
                    const dayData = workingHours[day];
                    return (
                        <div key={day} className="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                            <div className="flex items-center gap-2 w-32">
                                <input
                                    type="checkbox"
                                    id={`${day}-enabled`}
                                    checked={dayData.enabled}
                                    onChange={(e) => handleDayToggle(day, e.target.checked)}
                                    className="h-4 w-4 rounded border-gray-300"
                                />
                                <Label htmlFor={`${day}-enabled`} className="capitalize cursor-pointer">
                                    {day}
                                </Label>
                            </div>
                            {dayData.enabled && (
                                <div className="flex items-center gap-2 flex-1">
                                    <Input
                                        type="time"
                                        value={dayData.start}
                                        onChange={(e) => handleTimeChange(day, 'start', e.target.value)}
                                        required
                                        className="w-32"
                                    />
                                    <span className="text-gray-500">to</span>
                                    <Input
                                        type="time"
                                        value={dayData.end}
                                        onChange={(e) => handleTimeChange(day, 'end', e.target.value)}
                                        required
                                        className="w-32"
                                    />
                                </div>
                            )}
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
