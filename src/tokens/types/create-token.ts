import { Token } from '@/tokens/types/token';

export type CreateToken = Omit<
	Token,
	'id' | 'hash' | 'createdAt' | 'updatedAt'
>;
