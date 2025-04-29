import { render, screen } from '@testing-library/react';
import { MemoryRouter } from 'react-router-dom';
import MovieDetailsPage from '../MovieDetailsPage';

jest.mock('react-router-dom', () => ({
  ...jest.requireActual('react-router-dom'),
  useParams: () => ({ id: '1' }),
}));

jest.mock('../../hooks/useApi', () => ({
  useApi: () => ({
    data: {
      id: '1',
      title: 'A New Hope',
      episode_id: 4,
      characters: [{ id: '1', name: 'Luke Skywalker' }],
    },
    isLoading: false,
    error: undefined,
  }),
}));

describe('MovieDetailsPage', () => {
  it('renders movie title and character name', () => {
    render(
      <MemoryRouter>
        <MovieDetailsPage />
      </MemoryRouter>
    );
    expect(screen.getByText(/a new hope/i)).toBeInTheDocument();
    expect(screen.getByText(/luke skywalker/i)).toBeInTheDocument();
  });
}); 