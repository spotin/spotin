import { User } from '@/users/types/user';

export type LoginUser = Pick<User, 'email' | 'password'>;
