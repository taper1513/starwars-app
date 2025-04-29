import axios, { AxiosError } from 'axios';
import { useQuery, UseQueryOptions } from '@tanstack/react-query';

const API_BASE_URL = 'http://localhost:8000/api';

export interface ApiError {
  error: string;
  message: string;
  context?: Record<string, unknown>;
}

export function useApi<T>(
  endpoint: string,
  queryKey: unknown[],
  options?: Omit<UseQueryOptions<T, ApiError>, 'queryKey' | 'queryFn'>
) {
  return useQuery<T, ApiError>({
    queryKey,
    queryFn: async () => {
      try {
        const response = await axios.get<T>(`${API_BASE_URL}${endpoint}`);
        return response.data;
      } catch (error) {
        if (axios.isAxiosError(error)) {
          const axiosError = error as AxiosError<ApiError>;
          if (axiosError.response?.data) {
            throw axiosError.response.data;
          }
          throw {
            error: 'Network Error',
            message: 'Failed to connect to the server. Please check your internet connection.',
          };
        }
        throw {
          error: 'Unknown Error',
          message: 'An unexpected error occurred. Please try again later.',
        };
      }
    },
    retry: (failureCount, error) => {
      // Don't retry on 4xx errors
      if (error.error === 'Validation Error' || error.error?.startsWith('4')) {
        return false;
      }
      return failureCount < 3;
    },
    ...options,
  });
} 