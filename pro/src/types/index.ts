// src/types/index.ts
export interface User {
  id: number;
  full_name: string;
  username: string;
  email: string;
  phone?: string;
  birthdate?: string;
  gender?: 'male' | 'female';
  avatar?: string;
  user_type: 'owner' | 'tenant' | 'manager' | 'admin';
  building_address: string;
  floor: number;
  unit: number;
  is_active: boolean;
  email_verified_at?: string;
  last_login?: string;
  theme?: 'light' | 'dark' | 'auto';
  language?: 'fa' | 'en';
  timezone?: string;
  created_at: string;
  updated_at: string;
}

export interface AuthResponse {
  ok: boolean;
  token?: string;
  user?: User;
  message?: string;
  error_code?: string;
  expires_at?: string;
  response_time_ms?: number;
}

export interface LoginRequest {
  email: string;
  password: string;
  remember?: boolean;
}

export interface SignupRequest {
  full_name: string;
  email: string;
  phone: string;
  username: string;
  password: string;
  confirm_password: string;
  birthdate: string;
  gender: 'male' | 'female';
  building_address: string;
  user_type: 'owner' | 'tenant' | 'manager';
  floor: number;
  unit: number;
  terms: boolean;
}

export interface Transaction {
  id: number;
  user_id: number;
  type: 'income' | 'expense';
  amount: number;
  description: string;
  category: string;
  status: 'pending' | 'completed' | 'cancelled';
  created_at: string;
  updated_at: string;
}

export interface Message {
  id: number;
  sender_id: number;
  recipient_id: number;
  subject: string;
  content: string;
  is_read: boolean;
  created_at: string;
  updated_at: string;
}

export interface Activity {
  id: number;
  user_id: number;
  activity_type: string;
  description: string;
  ip_address?: string;
  user_agent?: string;
  created_at: string;
}

export interface Notification {
  id: number;
  user_id: number;
  title: string;
  message: string;
  type: 'info' | 'success' | 'warning' | 'error';
  is_read: boolean;
  created_at: string;
  updated_at: string;
}

export interface Task {
  id: number;
  title: string;
  description: string;
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled';
  priority: 'low' | 'medium' | 'high' | 'urgent';
  assigned_to: number;
  created_by: number;
  due_date?: string;
  created_at: string;
  updated_at: string;
}

export interface DashboardStats {
  transaction_count: number;
  unread_messages: number;
  pending_tasks: number;
  total_income: number;
  total_expense: number;
  balance: number;
}

export interface ApiResponse<T = any> {
  ok: boolean;
  data?: T;
  message?: string;
  error_code?: string;
  errors?: Record<string, string[]>;
}

export interface PaginationMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
}

export interface PaginatedResponse<T> extends ApiResponse<T[]> {
  meta: PaginationMeta;
}

export interface ValidationError {
  field: string;
  message: string;
}

export interface ThemeConfig {
  name: 'light' | 'dark' | 'auto';
  colors: {
    primary: string;
    secondary: string;
    success: string;
    warning: string;
    error: string;
    background: string;
    surface: string;
    text: string;
  };
}

export interface LanguageConfig {
  code: 'fa' | 'en';
  name: string;
  direction: 'rtl' | 'ltr';
}

export interface AppConfig {
  name: string;
  version: string;
  environment: 'development' | 'production' | 'testing';
  api_url: string;
  frontend_url: string;
  database: {
    host: string;
    port: number;
    name: string;
    username: string;
    password: string;
  };
  jwt: {
    secret: string;
    expires_in: string;
    refresh_expires_in: string;
  };
  oauth: {
    google: {
      client_id: string;
      client_secret: string;
      redirect_uri: string;
    };
    microsoft: {
      client_id: string;
      client_secret: string;
      redirect_uri: string;
    };
  };
}
