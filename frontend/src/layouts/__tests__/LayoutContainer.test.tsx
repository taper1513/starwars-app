import { render, screen } from '@testing-library/react';
import LayoutContainer from '../LayoutContainer';

describe('LayoutContainer', () => {
  it('renders children', () => {
    render(
      <LayoutContainer>
        <div>Test Child</div>
      </LayoutContainer>
    );
    expect(screen.getByText(/test child/i)).toBeInTheDocument();
  });
}); 