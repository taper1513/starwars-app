import { render, screen, fireEvent } from '@testing-library/react';
import SearchForm from '../SearchForm';

describe('SearchForm', () => {
  it('renders input and buttons', () => {
    render(<SearchForm onSearch={jest.fn()} />);
    expect(screen.getByPlaceholderText(/chewbacca/i)).toBeInTheDocument();
    expect(screen.getByRole('button', { name: /search/i })).toBeInTheDocument();
    expect(screen.getByLabelText(/people/i)).toBeInTheDocument();
    expect(screen.getByLabelText(/movies/i)).toBeInTheDocument();
  });

  it('calls onSearch with correct params', () => {
    const onSearch = jest.fn();
    render(<SearchForm onSearch={onSearch} />);
    fireEvent.change(screen.getByPlaceholderText(/chewbacca/i), { target: { value: 'Luke' } });
    const searchButton = screen.getByRole('button', { name: /search/i });
    fireEvent.click(searchButton);
    expect(onSearch).toHaveBeenCalledWith({ query: 'Luke', type: 'people' });
  });

  it('disables search button when input is empty', () => {
    render(<SearchForm onSearch={jest.fn()} />);
    const searchButton = screen.getByRole('button', { name: /search/i });
    expect(searchButton).toBeDisabled();
  });
}); 