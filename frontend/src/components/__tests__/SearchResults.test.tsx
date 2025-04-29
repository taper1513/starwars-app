import '@testing-library/jest-dom';
import { render, screen, fireEvent } from '@testing-library/react';
import { MemoryRouter } from 'react-router-dom';
import SearchResults from '../SearchResults';

const mockNavigate = jest.fn();
jest.mock('react-router-dom', () => ({
  ...jest.requireActual('react-router-dom'),
  useNavigate: () => mockNavigate,
}));

describe('SearchResults', () => {
  it('shows loading state', () => {
    render(
      <MemoryRouter>
        <SearchResults results={[]} type="people" isLoading />
      </MemoryRouter>
    );
    expect(screen.getByText(/searching/i)).toBeInTheDocument();
  });

  it('shows no results message', () => {
    render(
      <MemoryRouter>
        <SearchResults results={[]} type="people" />
      </MemoryRouter>
    );
    expect(screen.getByText(/there are zero matches/i)).toBeInTheDocument();
  });

  it('shows multiple results and allows navigation', () => {
    const results = [
      { id: '1', name: 'Luke Skywalker' },
      { id: '2', name: 'Leia Organa' },
    ];
    render(
      <MemoryRouter>
        <SearchResults results={results} type="people" />
      </MemoryRouter>
    );
    expect(screen.getByText(/luke skywalker/i)).toBeInTheDocument();
    expect(screen.getByText(/leia organa/i)).toBeInTheDocument();
    // Check that both SEE DETAILS buttons are present
    const buttons = screen.getAllByRole('button', { name: /see details/i });
    expect(buttons).toHaveLength(2);
  });

  it('calls navigation when SEE DETAILS is clicked', () => {
    const results = [{ id: '1', name: 'Luke Skywalker' }];
    render(
      <MemoryRouter>
        <SearchResults results={results} type="people" />
      </MemoryRouter>
    );
    fireEvent.click(screen.getByRole('button', { name: /see details/i }));
    expect(mockNavigate).toHaveBeenCalledWith('/people/1');
  });

  it('shows error fallback for unknown error', () => {
    render(
      <MemoryRouter>
        <SearchResults
          results={[]}
          type="people"
          error={{ error: 'Unknown Error', message: 'Something went wrong' }}
        />
      </MemoryRouter>
    );
    expect(screen.getByText(/unknown error/i)).toBeInTheDocument();
    expect(screen.getByText(/something went wrong/i)).toBeInTheDocument();
  });

  it('is accessible: results list has role list and items have role listitem', () => {
    const results = [
      { id: '1', name: 'Luke Skywalker' },
      { id: '2', name: 'Leia Organa' },
    ];
    render(
      <MemoryRouter>
        <SearchResults results={results} type="people" />
      </MemoryRouter>
    );
    // The list should be present
    const list = screen.getByRole('list');
    expect(list).toBeInTheDocument();
    // Each result should be a listitem
    const items = screen.getAllByRole('listitem');
    expect(items).toHaveLength(2);
  });

  it('shows error message for network error', () => {
    render(
      <MemoryRouter>
        <SearchResults
          results={[]}
          type="people"
          error={{ error: 'Network Error', message: 'Failed to connect' }}
        />
      </MemoryRouter>
    );
    expect(screen.getByText(/network error/i)).toBeInTheDocument();
    expect(screen.getByText(/failed to connect/i)).toBeInTheDocument();
    expect(screen.getByRole('button', { name: /retry/i })).toBeInTheDocument();
  });
}); 