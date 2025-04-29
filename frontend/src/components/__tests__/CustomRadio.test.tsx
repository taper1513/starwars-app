import { render, screen, fireEvent } from '@testing-library/react';
import { CustomRadio } from '../CustomRadio';

describe('CustomRadio', () => {
  it('renders with label and value', () => {
    render(
      <CustomRadio
        checked={false}
        onChange={jest.fn()}
        label="People"
        name="search-type"
        value="people"
      />
    );
    expect(screen.getByLabelText(/people/i)).toBeInTheDocument();
    expect(screen.getByRole('radio')).not.toBeChecked();
  });

  it('calls onChange when clicked', () => {
    const onChange = jest.fn();
    render(
      <CustomRadio
        checked={false}
        onChange={onChange}
        label="Movies"
        name="search-type"
        value="movies"
      />
    );
    fireEvent.click(screen.getByLabelText(/movies/i));
    expect(onChange).toHaveBeenCalled();
  });

  it('shows as checked when checked prop is true', () => {
    render(
      <CustomRadio
        checked={true}
        onChange={jest.fn()}
        label="People"
        name="search-type"
        value="people"
      />
    );
    expect(screen.getByRole('radio')).toBeChecked();
  });
}); 