import { User } from '@/users/types/user';

export type RegisterUser = Pick<User, 'username' | 'email'>;
