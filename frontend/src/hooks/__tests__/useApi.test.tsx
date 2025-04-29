import { renderHook, waitFor } from '@testing-library/react';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import axios from 'axios';
import { useApi } from '../useApi';

jest.mock('axios');
const mockedAxios = axios as jest.Mocked<typeof axios>;


const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      retry: false,
      gcTime: 0,   
      experimental_prefetchInRender: false
    },
  },
});

const wrapper = ({ children }: { children: React.ReactNode }) => (
  <QueryClientProvider client={queryClient}>{children}</QueryClientProvider>
);

describe('useApi', () => {
  afterEach(() => {
    jest.clearAllMocks();
    // Limpa o cache entre os testes
    queryClient.clear();
  });

  it('returns data on success', async () => {
    mockedAxios.get.mockResolvedValueOnce({ data: { foo: 'bar' } });
    const { result } = renderHook(() => useApi<{ foo: string }>('/test', ['test']), { wrapper });
    await waitFor(() => expect(result.current.data).toEqual({ foo: 'bar' }));
  });

  it('handles network error', async () => {
    const error = {
      isAxiosError: true,
      response: undefined,
      message: 'Network Error'
    };
    
    mockedAxios.get.mockRejectedValueOnce(error);
    jest.spyOn(axios, 'isAxiosError').mockReturnValue(true);

    const { result } = renderHook(() => useApi<{ foo: string }>('/test', ['test']), { wrapper });

    await waitFor(() => expect(result.current.failureReason?.error).toBeDefined());


    expect(result.current.failureReason?.error).toBe('Network Error');
  });
});