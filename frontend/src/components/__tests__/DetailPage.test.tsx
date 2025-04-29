import { render, screen } from '@testing-library/react';
import { MemoryRouter } from 'react-router-dom';
import DetailPage from '../DetailPage';

describe('DetailPage', () => {
  it('renders title, subtitle, side link, and back button', () => {
    render(
      <MemoryRouter>
        <DetailPage
          title="A New Hope"
          subtitles={[{ label: 'Episode', content: '4' }]}
          sideTitle="Characters"
          sideLinks={[{ id: '1', label: 'Luke Skywalker', onClick: () => {} }]}
          onBack={() => {}}
        />
      </MemoryRouter>
    );
    expect(screen.getByText(/a new hope/i)).toBeInTheDocument();
    expect(screen.getByText(/details/i)).toBeInTheDocument();
    expect(screen.getByText(/4/)).toBeInTheDocument();
    expect(screen.getByText(/characters/i)).toBeInTheDocument();
    expect(screen.getByText(/luke skywalker/i)).toBeInTheDocument();
    expect(screen.getByRole('button', { name: /back to search/i })).toBeInTheDocument();
  });
}); 