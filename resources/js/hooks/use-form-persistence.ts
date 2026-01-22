import { useEffect, useRef, useCallback } from 'react';

interface UseFormPersistenceOptions<T> {
    storageKey: string;
    formData: T;
    enabled?: boolean;
    debounceMs?: number;
    onRestore?: (data: T) => void;
}

interface UseFormPersistenceReturn<T> {
    savedData: T | null;
    hasSavedData: boolean;
    clearSavedData: () => void;
    restoreData: () => T | null;
}

/**
 * Hook to persist form data in sessionStorage and warn before page unload
 * 
 * @example
 * const { hasSavedData, savedData, clearSavedData } = useFormPersistence({
 *   storageKey: 'onboarding-draft',
 *   formData: form.data,
 *   onRestore: (data) => {
 *     if (confirm('Continue where you left off?')) {
 *       form.setData(data);
 *     }
 *   }
 * });
 */
export function useFormPersistence<T extends Record<string, unknown>>({
    storageKey,
    formData,
    enabled = true,
    debounceMs = 500,
    onRestore,
}: UseFormPersistenceOptions<T>): UseFormPersistenceReturn<T> {
    const debounceTimerRef = useRef<NodeJS.Timeout | null>(null);
    const hasRestoredRef = useRef(false);

    // Function to save data to sessionStorage
    const saveToStorage = useCallback((data: T) => {
        if (!enabled) return;

        try {
            sessionStorage.setItem(storageKey, JSON.stringify(data));
        } catch (error) {
            console.error('Failed to save form data to sessionStorage:', error);
        }
    }, [enabled, storageKey]);

    // Function to load data from sessionStorage
    const loadFromStorage = useCallback((): T | null => {
        if (!enabled) return null;

        try {
            const savedData = sessionStorage.getItem(storageKey);
            if (savedData) {
                return JSON.parse(savedData) as T;
            }
        } catch (error) {
            console.error('Failed to load form data from sessionStorage:', error);
        }
        return null;
    }, [enabled, storageKey]);

    // Clear saved data
    const clearSavedData = () => {
        try {
            sessionStorage.removeItem(storageKey);
        } catch (error) {
            console.error('Failed to clear form data from sessionStorage:', error);
        }
    };

    // Restore data manually
    const restoreData = (): T | null => {
        return loadFromStorage();
    };

    // Check if there's saved data
    const hasSavedData = !!loadFromStorage();
    const savedData = loadFromStorage();

    // Auto-restore on mount (once)
    useEffect(() => {
        if (!hasRestoredRef.current && enabled && onRestore) {
            const saved = loadFromStorage();
            if (saved) {
                onRestore(saved);
                hasRestoredRef.current = true;
            }
        }
    }, [enabled, onRestore, loadFromStorage]);

    // Debounced save on form data change
    useEffect(() => {
        if (!enabled) return;

        // Clear existing timer
        if (debounceTimerRef.current) {
            clearTimeout(debounceTimerRef.current);
        }

        // Set new timer
        debounceTimerRef.current = setTimeout(() => {
            saveToStorage(formData);
        }, debounceMs);

        // Cleanup
        return () => {
            if (debounceTimerRef.current) {
                clearTimeout(debounceTimerRef.current);
            }
        };
    }, [formData, enabled, debounceMs, saveToStorage]);

    // Add beforeunload warning
    useEffect(() => {
        if (!enabled) return;

        const handleBeforeUnload = (event: BeforeUnloadEvent) => {
            // Check if there's any data in sessionStorage
            const saved = loadFromStorage();
            if (saved) {
                event.preventDefault();
                // Modern browsers ignore custom messages, but still show a generic warning
                event.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return event.returnValue;
            }
        };

        window.addEventListener('beforeunload', handleBeforeUnload);

        return () => {
            window.removeEventListener('beforeunload', handleBeforeUnload);
        };
    }, [enabled, loadFromStorage]);

    return {
        savedData,
        hasSavedData,
        clearSavedData,
        restoreData,
    };
}
