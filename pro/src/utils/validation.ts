// src/utils/validation.ts
import { z } from 'zod';

// Common validation schemas
export const emailSchema = z
  .string()
  .min(1, 'ایمیل الزامی است')
  .email('فرمت ایمیل نامعتبر است')
  .max(255, 'ایمیل نمی‌تواند بیش از 255 کاراکتر باشد');

export const passwordSchema = z
  .string()
  .min(8, 'رمز عبور باید حداقل 8 کاراکتر باشد')
  .max(100, 'رمز عبور نمی‌تواند بیش از 100 کاراکتر باشد')
  .regex(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/, 'رمز عبور باید شامل حروف بزرگ، کوچک و عدد باشد');

export const phoneSchema = z
  .string()
  .min(1, 'شماره تلفن الزامی است')
  .regex(/^09\d{9}$/, 'شماره تلفن باید با 09 شروع شود و 11 رقم باشد');

export const persianNameSchema = z
  .string()
  .min(2, 'نام باید حداقل 2 کاراکتر باشد')
  .max(50, 'نام نمی‌تواند بیش از 50 کاراکتر باشد')
  .regex(/^[\u0600-\u06FF\s]+$/, 'نام باید فارسی باشد');

export const usernameSchema = z
  .string()
  .min(3, 'نام کاربری باید حداقل 3 کاراکتر باشد')
  .max(20, 'نام کاربری نمی‌تواند بیش از 20 کاراکتر باشد')
  .regex(/^[a-zA-Z0-9_]+$/, 'نام کاربری باید فقط شامل حروف، اعداد و _ باشد');

export const birthdateSchema = z
  .string()
  .min(1, 'تاریخ تولد الزامی است')
  .refine((date) => {
    const selectedDate = new Date(date);
    const today = new Date();
    return selectedDate < today;
  }, 'تاریخ تولد باید قبل از امروز باشد');

// Login validation schema
export const loginSchema = z.object({
  email: emailSchema,
  password: z.string().min(1, 'رمز عبور الزامی است'),
  remember: z.boolean().optional(),
});

// Signup validation schema
export const signupSchema = z.object({
  full_name: persianNameSchema,
  email: emailSchema,
  phone: phoneSchema,
  username: usernameSchema,
  password: passwordSchema,
  confirm_password: z.string().min(1, 'تکرار رمز عبور الزامی است'),
  birthdate: birthdateSchema,
  gender: z.enum(['male', 'female'], {
    errorMap: () => ({ message: 'انتخاب جنسیت الزامی است' }),
  }),
  building_address: z.string().min(1, 'آدرس ساختمان الزامی است'),
  user_type: z.enum(['owner', 'tenant', 'manager'], {
    errorMap: () => ({ message: 'انتخاب نوع کاربری الزامی است' }),
  }),
  floor: z.number().min(1, 'شماره طبقه باید حداقل 1 باشد'),
  unit: z.number().min(1, 'شماره واحد باید حداقل 1 باشد'),
  terms: z.boolean().refine((val) => val === true, {
    message: 'باید با قوانین و مقررات موافقت کنید',
  }),
}).refine((data) => data.password === data.confirm_password, {
  message: 'رمز عبور و تکرار آن مطابقت ندارند',
  path: ['confirm_password'],
});

// Password strength checker
export function checkPasswordStrength(password: string): {
  score: number;
  level: 'weak' | 'medium' | 'strong' | 'very_strong';
  suggestions: string[];
} {
  let score = 0;
  const suggestions: string[] = [];

  // Length check
  if (password.length >= 8) {
    score++;
  } else {
    suggestions.push('حداقل 8 کاراکتر استفاده کنید');
  }

  if (password.length >= 12) {
    score++;
  }

  // Character variety checks
  if (/[a-z]/.test(password)) {
    score++;
  } else {
    suggestions.push('حداقل یک حرف کوچک اضافه کنید');
  }

  if (/[A-Z]/.test(password)) {
    score++;
  } else {
    suggestions.push('حداقل یک حرف بزرگ اضافه کنید');
  }

  if (/\d/.test(password)) {
    score++;
  } else {
    suggestions.push('حداقل یک عدد اضافه کنید');
  }

  if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
    score++;
  } else {
    suggestions.push('برای امنیت بیشتر، از کاراکترهای خاص استفاده کنید');
  }

  // Determine level
  let level: 'weak' | 'medium' | 'strong' | 'very_strong';
  if (score <= 2) {
    level = 'weak';
  } else if (score <= 4) {
    level = 'medium';
  } else if (score <= 6) {
    level = 'strong';
  } else {
    level = 'very_strong';
  }

  return { score, level, suggestions };
}

// Email availability checker
export async function checkEmailAvailability(email: string): Promise<{
  available: boolean;
  message: string;
}> {
  try {
    const response = await fetch('/api/auth/check-email', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ email }),
    });

    const data = await response.json();
    return {
      available: data.available,
      message: data.message,
    };
  } catch (error) {
    return {
      available: false,
      message: 'خطا در بررسی ایمیل',
    };
  }
}

// Username availability checker
export async function checkUsernameAvailability(username: string): Promise<{
  available: boolean;
  message: string;
}> {
  try {
    const response = await fetch('/api/auth/check-username', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ username }),
    });

    const data = await response.json();
    return {
      available: data.available,
      message: data.message,
    };
  } catch (error) {
    return {
      available: false,
      message: 'خطا در بررسی نام کاربری',
    };
  }
}

// Form validation helper
export function validateForm<T>(
  schema: z.ZodSchema<T>,
  data: unknown
): {
  success: boolean;
  data?: T;
  errors?: Record<string, string[]>;
} {
  try {
    const validatedData = schema.parse(data);
    return {
      success: true,
      data: validatedData,
    };
  } catch (error) {
    if (error instanceof z.ZodError) {
      const errors: Record<string, string[]> = {};
      
      error.errors.forEach((err) => {
        const path = err.path.join('.');
        if (!errors[path]) {
          errors[path] = [];
        }
        errors[path].push(err.message);
      });

      return {
        success: false,
        errors,
      };
    }
    
    return {
      success: false,
      errors: { general: ['خطا در اعتبارسنجی'] },
    };
  }
}

// Real-time validation helper
export function validateField<T>(
  schema: z.ZodSchema<T>,
  value: unknown,
  fieldName: string
): {
  valid: boolean;
  message?: string;
} {
  try {
    schema.parse(value);
    return { valid: true };
  } catch (error) {
    if (error instanceof z.ZodError) {
      const fieldError = error.errors.find(err => 
        err.path.includes(fieldName) || err.path.length === 0
      );
      return {
        valid: false,
        message: fieldError?.message || 'مقدار نامعتبر',
      };
    }
    
    return {
      valid: false,
      message: 'خطا در اعتبارسنجی',
    };
  }
}
