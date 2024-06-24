import { Token } from '@/tokens/types/token';

export type ReadToken = Omit<Token, 'hash'>;
