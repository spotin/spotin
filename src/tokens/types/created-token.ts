import { Token } from '@/tokens/types/token';

export type CreatedToken = Omit<Token, 'hash'> & {
	value: string;
};
