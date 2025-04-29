import { render, screen } from '@testing-library/react';
import { MemoryRouter } from 'react-router-dom';
import PersonDetailsPage from '../PersonDetailsPage';

jest.mock('react-router-dom', () => ({
  ...jest.requireActual('react-router-dom'),
  useParams: () => ({ id: '1' }),
}));

jest.mock('../../hooks/useApi', () => ({
  useApi: () => ({
    data: {
      id: '1',
      name: 'Luke Skywalker',
      birth_year: '19BBY',
      films: [{ id: '1', title: 'A New Hope' }],
    },
    isLoading: false,
    error: undefined,
  }),
}));

describe('PersonDetailsPage', () => {
  it('renders person name and film title', () => {
    render(
      <MemoryRouter>
        <PersonDetailsPage />
      </MemoryRouter>
    );
    expect(screen.getByText(/luke skywalker/i)).toBeInTheDocument();
    expect(screen.getByText(/a new hope/i)).toBeInTheDocument();
  });
}); 