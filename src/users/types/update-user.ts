import { User } from '@/users/types/user';

export type UpdateUser = Partial<Omit<User, 'id' | 'createdAt' | 'updatedAt'>>;
